from flask import Flask, render_template
import random

app = Flask(__name__)

# list of cat images
images = [
    "https://img.buzzfeed.com/buzzfeed-static/static/2015-11/16/8/campaign_images/webdr11/19-motivos-que-fazem-gatos-serem-melhores-do-que--1-16042-1447681191-0_big.jpg?output-format=auto&output-quality=auto&resize=300:*;",
    "https://img.buzzfeed.com/buzzfeed-static/static/2013-11/campaign_images/webdr02/11/16/grumpy-cat-tem-o-pior-dia-de-todos-os-tempos-na-d-1-16133-1384204659-10_big.jpg?output-format=auto&output-quality=auto&resize=300:*;",
    "https://img.buzzfeed.com/buzzfeed-static/static/2019-03/25/17/campaign_images/buzzfeed-prod-web-01/por-que-voce-deveria-dar-um-microfone-para-o-seu--2-1108-1553548816-0_dblbig.jpg?output-format=auto&output-quality=auto&resize=300:*;",
]


@app.route("/")
def index():
    url = random.choice(images)
    return render_template("index.html", url=url)


if __name__ == "__main__":
    app.run(host="0.0.0.0")
