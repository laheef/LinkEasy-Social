#!/usr/bin/env python3
"""Builds public/assets/img/og-image.png (1200x630) with the current brand mark."""
import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

REPO = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(REPO, 'public/assets/img')
W, H = 1200, 630
BG = (15, 14, 19)
RED = (252, 36, 40)


def font(weight, size):
    inter = os.path.join(REPO, 'tools/sources/Inter.ttf')
    if os.path.exists(inter):
        f = ImageFont.truetype(inter, size)
        try:
            f.set_variation_by_axes([14, 800 if weight == 'Bold' else 600])
        except Exception:
            pass
        return f
    c = f'/usr/share/fonts/truetype/dejavu/DejaVuSans-{weight}.ttf'
    return ImageFont.truetype(c, size) if os.path.exists(c) else ImageFont.load_default()


def main(host='laheef.dev'):
    img = Image.new('RGBA', (W, H), BG + (255,))

    # subtle grid (own overlay so alpha blends correctly)
    grid = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    gd0 = ImageDraw.Draw(grid)
    for x in range(0, W, 60):
        gd0.line([(x, 0), (x, H)], fill=(255, 255, 255, 12))
    for y in range(0, H, 60):
        gd0.line([(0, y), (W, y)], fill=(255, 255, 255, 12))
    img = Image.alpha_composite(img, grid)

    # red glow, upper-right
    glow = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    gd = ImageDraw.Draw(glow)
    gd.ellipse([760, -360, 1500, 420], fill=(*RED, 70))
    glow = glow.filter(ImageFilter.GaussianBlur(120))
    img = Image.alpha_composite(img, glow)
    draw = ImageDraw.Draw(img, 'RGBA')

    # logo (light variant)
    mark = Image.open(os.path.join(IMG, 'logo-mark-light.png')).convert('RGBA')
    ms = 104
    mark = mark.resize((ms, ms), Image.LANCZOS)
    img.alpha_composite(mark, (96, 78))
    draw.text((96 + ms + 22, 96), 'LinkEasy', font=font('Bold', 56), fill=(255, 255, 255, 255))
    fb = draw.textbbox((96 + ms + 22, 96), 'LinkEasy', font=font('Bold', 56))
    draw.text((fb[2] + 8, 96), 'Social', font=font('Bold', 56), fill=(*RED, 255))

    # headline
    hf = font('Bold', 76)
    draw.text((96, 270), 'Create. Schedule.', font=hf, fill=(255, 255, 255, 255))
    draw.text((96, 360), 'Publish. Analyze.', font=hf, fill=(*RED, 255))

    sf = font('Bold', 30)
    draw.text((96, 480), 'One workspace for every social platform.',
              font=sf, fill=(180, 182, 192, 255))

    # host chip, bottom-right (overlay for translucent fill/outline)
    chip_font = font('Bold', 26)
    txt = host
    tb = draw.textbbox((0, 0), txt, font=chip_font)
    cw, ch = tb[2] - tb[0] + 44, tb[3] - tb[1] + 24
    cx0, cy0 = W - 96 - cw, H - 92 - ch
    chip = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    cd = ImageDraw.Draw(chip)
    cd.rounded_rectangle([cx0, cy0, cx0 + cw, cy0 + ch], radius=ch // 2,
                         fill=(255, 255, 255, 26), outline=(255, 255, 255, 60), width=1)
    img = Image.alpha_composite(img, chip)
    draw = ImageDraw.Draw(img, 'RGBA')
    draw.text((cx0 + 22, cy0 + 12), txt, font=chip_font, fill=(235, 236, 242, 255))

    out = os.path.join(IMG, 'og-image.png')
    img.convert('RGB').save(out, optimize=True)
    print('wrote', out)


if __name__ == '__main__':
    import sys
    main(sys.argv[1] if len(sys.argv) > 1 else 'laheef.dev')
