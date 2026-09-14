#!/usr/bin/env python3
"""
Builds LinkEasy Social brand assets from the source 3D logo (RGB PNG where the
mark sits on a pure-black square):

  public/assets/img/logo-mark.png/.webp       black background REMOVED, original
                                              charcoal + red mark (light backgrounds)
  public/assets/img/logo-mark-light.png/.webp black removed, slate ribbon recolored
                                              white with red chevron kept (dark backgrounds)
  public/assets/img/favicon.ico               16/32/48/64 transparent cut
  public/assets/img/favicon-32.png            transparent cut
  public/assets/img/favicon-180.png           apple touch (opaque full-bleed tile)
  public/assets/img/favicon-192/512.png       PWA (opaque full-bleed black tile)

The 3D L-ribbon fades into near-black at its base, so a luminance key tears the
silhouette apart. Instead we flood-fill dark pixels (lum <= FLOOD_T) from the
edges to find the connected background, and treat everything the flood cannot
reach as part of the mark. A low threshold keeps the fading base attached; a
median pass drops flood noise; a sub-pixel feather anti-aliases the edge.

Usage: python3 tools/build_logo.py [source.png]
"""
import os
import sys
import numpy as np
from PIL import Image, ImageDraw, ImageFilter

REPO = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(REPO, 'public/assets/img')
DARK_BG = (12, 11, 16)
FLOOD_T = 12   # pixels darker than this may belong to the background


def load_mark(src: str, recolor=None) -> Image.Image:
    """Cut the mark off its black square (see module docstring).

    recolor: None keeps original colors; (255,255,255) maps the non-red slate
    ribbon to that color (for dark backgrounds)."""
    src_im = Image.open(src).convert('RGB')
    w, h = src_im.size
    sp = src_im.load()

    # 1. Candidate background: very dark pixels
    cand = Image.new('L', (w, h), 0)
    cp = cand.load()
    for y in range(h):
        for x in range(w):
            if max(sp[x, y]) <= FLOOD_T:
                cp[x, y] = 255
    # 2. Flood from the corner: only dark pixels *connected to an edge* are bg.
    #    ImageDraw.floodfill must run on a PIL image with pixel access — it
    #    silently no-ops on numpy-backed images in Pillow 12.
    ImageDraw.floodfill(cand, (0, 0), 128, thresh=0)
    reached = np.array(cand) == 128

    # 3. Binary silhouette, denoise, then feather ~0.5px for AA edges
    sil = Image.fromarray(np.where(reached, 0, 255).astype('uint8'), 'L')
    sil = sil.filter(ImageFilter.MedianFilter(3))
    sil = sil.filter(ImageFilter.GaussianBlur(0.5))
    alpha = np.array(sil).astype(float) / 255.0

    rgba = np.array(src_im).astype(float)
    if recolor is not None:
        r, g, b = rgba[:, :, 0], rgba[:, :, 1], rgba[:, :, 2]
        is_red = (r > 45) & (r > g * 1.4) & (r > b * 1.4)
        rgba = np.dstack([
            np.where(is_red, r, recolor[0]),
            np.where(is_red, g, recolor[1]),
            np.where(is_red, b, recolor[2]),
        ])
    rgba = np.dstack([rgba, alpha * 255]).astype('uint8')
    out = Image.fromarray(rgba, 'RGBA')
    bbox = out.getbbox()
    return out.crop(bbox) if bbox else out


def square_tile(mark: Image.Image, size: int, bg=(8, 8, 10)) -> Image.Image:
    """Full-bleed opaque tile (apple/PWA apply their own masking)."""
    tile = Image.new('RGBA', (size, size), bg + (255,))
    inner = int(size * 0.86)
    m = mark.resize((inner, inner), Image.LANCZOS)
    tile.alpha_composite(m, ((size - inner) // 2, (size - inner) // 2))
    return tile


def transparent_mark(mark: Image.Image, size: int, inset: float) -> Image.Image:
    canvas = Image.new('RGBA', (size, size), (0, 0, 0, 0))
    inner = int(size * (1 - 2 * inset))
    m = mark.resize((inner, inner), Image.LANCZOS)
    canvas.alpha_composite(m, ((size - inner) // 2, (size - inner) // 2))
    return canvas


def main(src):
    os.makedirs(IMG, exist_ok=True)
    mark = load_mark(src)
    mark_light = load_mark(src, recolor=(255, 255, 255))

    # Light backgrounds: black keyed out, original charcoal + red 3D mark
    cut = transparent_mark(mark, 256, 0.03)
    cut.save(os.path.join(IMG, 'logo-mark.png'))
    cut.save(os.path.join(IMG, 'logo-mark.webp'), lossless=True)

    # Dark backgrounds: slate ribbon mapped to white, red kept
    light = transparent_mark(mark_light, 256, 0.04)
    light.save(os.path.join(IMG, 'logo-mark-light.png'))
    light.save(os.path.join(IMG, 'logo-mark-light.webp'), lossless=True)

    # Favicons — same transparent cuts
    transparent_mark(mark, 32, 0.02).save(os.path.join(IMG, 'favicon-32.png'))
    ico64 = transparent_mark(mark, 64, 0.02)
    ico64.save(os.path.join(IMG, 'favicon.ico'),
               sizes=[(16, 16), (32, 32), (48, 48), (64, 64)])

    # Apple touch / PWA must be opaque — full-bleed black tile
    square_tile(mark, 180).convert('RGB').save(os.path.join(IMG, 'favicon-180.png'))
    square_tile(mark, 192, bg=DARK_BG).convert('RGB').save(os.path.join(IMG, 'favicon-192.png'))
    square_tile(mark, 512, bg=DARK_BG).convert('RGB').save(os.path.join(IMG, 'favicon-512.png'))

    # high-res master cut
    os.makedirs(os.path.join(REPO, 'storage/build'), exist_ok=True)
    mark.save(os.path.join(REPO, 'storage/build/logo-cutout.png'))
    print('brand assets written to', IMG)


if __name__ == '__main__':
    src = sys.argv[1] if len(sys.argv) > 1 else \
        os.path.join(REPO, 'tools/sources/logo-source.png')
    main(src)
