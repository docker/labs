from flask import Flask, render_template
import random, requests, json

app = Flask(__name__)

# function to create a list of urls from catsaas.com
def get_cats(r):
   cats = []
   data = json.loads(r.text)
   for cat in data:
     cats.append("https://cataas.com/cat/" + cat['id'])
   return cats

# get list of cat images from cataas.com
r = requests.get('https://cataas.com/api/cats?limit=25')

# call function to create list of cat urls
images = get_cats(r)

@app.route('/')
def index():
    url = random.choice(images)
    return render_template('index.html', url=url)

if __name__ == "__main__":
    app.run(host="0.0.0.0")
