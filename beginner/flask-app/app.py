from flask import Flask, render_template
import random

app = Flask(__name__)

# list of cat images
images = [
    "https://upload.wikimedia.org/wikipedia/commons/b/bb/Kittyply_edit1.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Felis_silvestris_catus_lying_on_rice_straw.jpg/1920px-Felis_silvestris_catus_lying_on_rice_straw.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Domestic_Cat_Face_Shot.jpg/1920px-Domestic_Cat_Face_Shot.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/d/da/Cat_tongue_macro.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/3/3b/Gato_enervado_pola_presencia_dun_can.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Kot_z_mysz%C4%85.jpg/1024px-Kot_z_mysz%C4%85.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Large_Ragdoll_cat_tosses_a_mouse.jpg/1920px-Large_Ragdoll_cat_tosses_a_mouse.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/b/b5/1dayoldkitten.JPG",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Felis_catus-cat_on_snow.jpg/1920px-Felis_catus-cat_on_snow.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/7/76/TapetumLucidum.JPG",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Domestic_Cat_Demonstrating_Dilated_Slit_Pupils.jpg/800px-Domestic_Cat_Demonstrating_Dilated_Slit_Pupils.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Black_Cat_%287983739954%29.jpg/1920px-Black_Cat_%287983739954%29.jpg"
]

@app.route('/')
def index():
    url = random.choice(images)
    return render_template('index.html', url=url)

if __name__ == "__main__":
    app.run(host="0.0.0.0")
