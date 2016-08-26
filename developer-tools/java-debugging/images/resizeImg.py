#!/usr/bin/env python

import sys, os
from PIL import Image

print sys.argv

imgName = sys.argv[1]
reduceWidth = float(sys.argv[2])
reduceHeight = float(sys.argv[3])
outName = sys.argv[4]

img = Image.open(imgName)
width, height = img.size
reducedImg = img.resize((int(width/reduceWidth),int(height/reduceHeight)))
reducedImg.save(outName)

