/*
 * Copyright (c) 2015, Yahoo Inc. All rights reserved.
 * Copyrights licensed under the New BSD License.
 * See the accompanying LICENSE file for terms.
 */

'use strict';

var Promise = global.Promise || require('promise');

var glob       = require('glob');
var Handlebars = require('handlebars');
var fs         = require('graceful-fs');
var path       = require('path');

var utils = require('./utils');

module.exports = ExpressHandlebars;

// -----------------------------------------------------------------------------

function ExpressHandlebars(config) {
    // Config properties with defaults.
    utils.assign(this, {
        handlebars     : Handlebars,
        extname        : '.handlebars',
        layoutsDir     : 'views/layouts/',
        partialsDir    : 'views/partials/',
        defaultLayout  : undefined,
        helpers        : undefined,
        compilerOptions: undefined,
    }, config);

    // Express view engine integration point.
    this.engine = this.renderView.bind(this);

    // Normalize `extname`.
    if (this.extname.charAt(0) !== '.') {
        this.extname = '.' + this.extname;
    }

    // Internal caches of compiled and precompiled templates.
    this.compiled    = Object.create(null);
    this.precompiled = Object.create(null);

    // Private internal file system cache.
    this._fsCache = Object.create(null);
}

ExpressHandlebars.prototype.getPartials = function (options) {
    var partialsDirs = Array.isArray(this.partialsDir) ?
            this.partialsDir : [this.partialsDir];

    partialsDirs = partialsDirs.map(function (dir) {
        var dirPath;
        var dirTemplates;
        var dirNamespace;

        // Support `partialsDir` collection with object entries that contain a
        // templates promise and a namespace.
        if (typeof dir === 'string') {
            dirPath = dir;
        } else if (typeof dir === 'object') {
            dirTemplates = dir.templates;
            dirNamespace = dir.namespace;
            dirPath      = dir.dir;
        }

        // We must have some path to templates, or templates themselves.
        if (!(dirPath || dirTemplates)) {
            throw new Error('A partials dir must be a string or config object');
        }

        // Make sure we're have a promise for the templates.
        var templatesPromise = dirTemplates ? Promise.resolve(dirTemplates) :
                this.getTemplates(dirPath, options);

        return templatesPromise.then(function (templates) {
            return {
                templates: templates,
                namespace: dirNamespace,
            };
        });
    }, this);

    return Promise.all(partialsDirs).then(function (dirs) {
        var getTemplateName = this._getTemplateName.bind(this);

        return dirs.reduce(function (partials, dir) {
            var templates = dir.templates;
            var namespace = dir.namespace;
            var filePaths = Object.keys(templates);

            filePaths.forEach(function (filePath) {
                var partialName       = getTemplateName(filePath, namespace);
                partials[partialName] = templates[filePath];
            });

            return partials;
        }, {});
    }.bind(this));
};

ExpressHandlebars.prototype.getTemplate = function (filePath, options) {
    filePath = path.resolve(filePath);
    options || (options = {});

    var precompiled = options.precompiled;
    var cache       = precompiled ? this.precompiled : this.compiled;
    var template    = options.cache && cache[filePath];

    if (template) {
        return template;
    }

    // Optimistically cache template promise to reduce file system I/O, but
    // remove from cache if there was a problem.
    template = cache[filePath] = this._getFile(filePath, {cache: options.cache})
        .then(function (file) {
            if (precompiled) {
                return this._precompileTemplate(file, this.compilerOptions);
            }

            return this._compileTemplate(file, this.compilerOptions);
        }.bind(this));

    return template.catch(function (err) {
        delete cache[filePath];
        throw err;
    });
};

ExpressHandlebars.prototype.getTemplates = function (dirPath, options) {
    options || (options = {});
    var cache = options.cache;

    return this._getDir(dirPath, {cache: cache}).then(function (filePaths) {
        var templates = filePaths.map(function (filePath) {
            return this.getTemplate(path.join(dirPath, filePath), options);
        }, this);

        return Promise.all(templates).then(function (templates) {
            return filePaths.reduce(function (hash, filePath, i) {
                hash[filePath] = templates[i];
                return hash;
            }, {});
        });
    }.bind(this));
};

ExpressHandlebars.prototype.render = function (filePath, context, options) {
    options || (options = {});

    return Promise.all([
        this.getTemplate(filePath, {cache: options.cache}),
        options.partials || this.getPartials({cache: options.cache}),
    ]).then(function (templates) {
        var template = templates[0];
        var partials = templates[1];
        var helpers  = options.helpers || this.helpers;

        // Add ExpressHandlebars metadata to the data channel so that it's
        // accessible within the templates and helpers, namespaced under:
        // `@exphbs.*`
        var data = utils.assign({}, options.data, {
            exphbs: utils.assign({}, options, {
                filePath: filePath,
                helpers : helpers,
                partials: partials,
            }),
        });

        return this._renderTemplate(template, context, {
            data    : data,
            helpers : helpers,
            partials: partials,
        });
    }.bind(this));
};

ExpressHandlebars.prototype.renderView = function (viewPath, options, callback) {
    options || (options = {});

    var context = options;

    // Express provides `settings.views` which is the path to the views dir that
    // the developer set on the Express app. When this value exists, it's used
    // to compute the view's name.
    var view;
    var viewsPath = options.settings && options.settings.views;
    if (viewsPath) {
        view = this._getTemplateName(path.relative(viewsPath, viewPath));
    }

    // Merge render-level and instance-level helpers together.
    var helpers = utils.assign({}, this.helpers, options.helpers);

    // Merge render-level and instance-level partials together.
    var partials = Promise.all([
        this.getPartials({cache: options.cache}),
        Promise.resolve(options.partials),
    ]).then(function (partials) {
        return utils.assign.apply(null, [{}].concat(partials));
    });

    // Pluck-out ExpressHandlebars-specific options and Handlebars-specific
    // rendering options.
    options = {
        cache : options.cache,
        view  : view,
        layout: 'layout' in options ? options.layout : this.defaultLayout,

        data    : options.data,
        helpers : helpers,
        partials: partials,
    };

    this.render(viewPath, context, options)
        .then(function (body) {
            var layoutPath = this._resolveLayoutPath(options.layout);

            if (layoutPath) {
                return this.render(
                    layoutPath,
                    utils.assign({}, context, {body: body}),
                    utils.assign({}, options, {layout: undefined})
                );
            }

            return body;
        }.bind(this))
        .then(utils.passValue(callback))
        .catch(utils.passError(callback));
};

// -- Protected Hooks ----------------------------------------------------------

ExpressHandlebars.prototype._compileTemplate = function (template, options) {
    return this.handlebars.compile(template, options);
};

ExpressHandlebars.prototype._precompileTemplate = function (template, options) {
    return this.handlebars.precompile(template, options);
};

ExpressHandlebars.prototype._renderTemplate = function (template, context, options) {
    return template(context, options);
};

// -- Private ------------------------------------------------------------------

ExpressHandlebars.prototype._getDir = function (dirPath, options) {
    dirPath = path.resolve(dirPath);
    options || (options = {});

    var cache = this._fsCache;
    var dir   = options.cache && cache[dirPath];

    if (dir) {
        return dir.then(function (dir) {
            return dir.concat();
        });
    }

    var pattern = '**/*' + this.extname;

    // Optimistically cache dir promise to reduce file system I/O, but remove
    // from cache if there was a problem.
    dir = cache[dirPath] = new Promise(function (resolve, reject) {
        glob(pattern, {
            cwd   : dirPath,
            follow: true
        }, function (err, dir) {
            if (err) {
                reject(err);
            } else {
                resolve(dir);
            }
        });
    });

    return dir.then(function (dir) {
        return dir.concat();
    }).catch(function (err) {
        delete cache[dirPath];
        throw err;
    });
};

ExpressHandlebars.prototype._getFile = function (filePath, options) {
    filePath = path.resolve(filePath);
    options || (options = {});

    var cache = this._fsCache;
    var file  = options.cache && cache[filePath];

    if (file) {
        return file;
    }

    // Optimistically cache file promise to reduce file system I/O, but remove
    // from cache if there was a problem.
    file = cache[filePath] = new Promise(function (resolve, reject) {
        fs.readFile(filePath, 'utf8', function (err, file) {
            if (err) {
                reject(err);
            } else {
                resolve(file);
            }
        });
    });

    return file.catch(function (err) {
        delete cache[filePath];
        throw err;
    });
};

ExpressHandlebars.prototype._getTemplateName = function (filePath, namespace) {
    var extRegex = new RegExp(this.extname + '$');
    var name     = filePath.replace(extRegex, '');

    if (namespace) {
        name = namespace + '/' + name;
    }

    return name;
};

ExpressHandlebars.prototype._resolveLayoutPath = function (layoutPath) {
    if (!layoutPath) {
        return null;
    }

    if (!path.extname(layoutPath)) {
        layoutPath += this.extname;
    }

    return path.resolve(this.layoutsDir, layoutPath);
};
