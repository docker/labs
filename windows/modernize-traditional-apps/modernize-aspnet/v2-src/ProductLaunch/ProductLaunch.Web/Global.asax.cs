using ProductLaunch.Model;
using ProductLaunch.Model.Initializers;
using System;
using System.Data.Entity;
using System.Web;
using System.Web.Optimization;
using System.Web.Routing;

namespace ProductLaunch.Web
{
    public class Global : HttpApplication
    {
        void Application_Start(object sender, EventArgs e)
        {
            // Code that runs on application startup
            RouteConfig.RegisterRoutes(RouteTable.Routes);
            BundleConfig.RegisterBundles(BundleTable.Bundles);

            Database.SetInitializer<ProductLaunchContext>(new StaticDataInitializer());
            SignUp.PreloadStaticDataCache();
        }
    }
}