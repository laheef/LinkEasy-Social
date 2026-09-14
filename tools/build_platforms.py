#!/usr/bin/env python3
"""
Builds LinkEasy Social's social-platform assets from Simple Icons (CC0):

  config/platforms.generated.php      single source of truth (slug/name/color)
  views/partials/icons-platforms.php   SVG sprite symbols (#i-<slug>)
  public/assets/css/platforms.css     brand-tile colors + platform matrix styles

Usage:
  1. download icons into a directory:
       mkdir -p storage/build/si
       for s in <si-slugs...>; do curl -sL -o storage/build/si/$s.svg \
         https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/$s.svg; done
  2. python3 tools/build_platforms.py tools/sources/si

Platforms without a Simple Icon (letter in PLATFORMS with si=None) get a
monogram <text> glyph instead, so every tile still renders.
"""
import os
import re
import sys

REPO = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# (internal slug, display name, simple-icons file or None, available now?,
#  tile hex color, dark glyph needed?, monogram letter or None)
PLATFORMS = [
    # ---- Available connections ------------------------------------------
    ('instagram',       'Instagram',       'instagram',       True,  None,      False, None),  # gradient tile in CSS
    ('facebook',        'Facebook',        'facebook',        True,  '1877F2',  False, None),
    ('tiktok',          'TikTok',          'tiktok',          True,  '111114',  False, None),
    ('linkedin',        'LinkedIn',        'linkedin',        True,  '0A66C2',  False, None),
    ('youtube',         'YouTube',         'youtube',         True,  'FF0000',  False, None),
    ('pinterest',       'Pinterest',       'pinterest',       True,  'E60023',  False, None),
    ('x',               'X',               'x',               True,  '000000',  False, None),
    ('threads',         'Threads',         'threads',         True,  '000000',  False, None),
    ('google-business', 'Google Business', 'googlemybusiness', True, '4285F4',  False, None),

    # ---- Social networks & microblogging --------------------------------
    ('bluesky',         'Bluesky',         'bluesky',         True, '0285FF',  False, None),
    ('reddit',          'Reddit',          'reddit',          False, 'FF4500',  False, None),
    ('telegram',        'Telegram',        'telegram',        False, '26A5E4',  False, None),
    ('discord',         'Discord',         'discord',         False, '5865F2',  False, None),
    ('whatsapp',        'WhatsApp',        'whatsapp',        True, '25D366',  False, None),
    ('snapchat',        'Snapchat',        'snapchat',        False, 'FFFC00',  True,  None),
    ('wechat',          'WeChat',          'wechat',          False, '07C160',  False, None),
    ('mastodon',        'Mastodon',        'mastodon',        False, '6364FF',  False, None),
    ('lemmy',           'Lemmy',           'lemmy',           False, '1D68D9',  False, None),
    ('misskey',         'Misskey',         'misskey',         False, '86B300',  True,  None),
    ('pixelfed',        'Pixelfed',        'pixelfed',        False, '6366F1',  False, None),
    ('peertube',        'PeerTube',        'peertube',        False, 'F1680D',  False, None),
    ('firefish',        'Firefish',        'firefish',        False, 'F07A5B',  True,  None),
    ('pleroma',         'Pleroma',         'pleroma',         False, 'D2793C',  False, None),
    ('nostr',           'Nostr',           None,              False, '7C3AED',  False, 'N'),
    ('mewe',            'MeWe',            'mewe',            False, '17377F',  False, None),
    ('gab',             'Gab',             None,              False, '21CF7B',  True,  'G'),
    ('truthsocial',     'Truth Social',    None,              False, '2A48B5',  False, 'T'),

    # ---- Live & video ----------------------------------------------------
    ('twitch',          'Twitch',          'twitch',          False, '9146FF',  False, None),
    ('kick',            'Kick',            'kick',            False, '53FC19',  True,  None),
    ('vimeo',          'Vimeo',           'vimeo',           False, '1AB7EA',  False, None),
    ('dailymotion',     'Dailymotion',     'dailymotion',     False, '0066DC',  False, None),
    ('rumble',          'Rumble',          'rumble',          False, '85C742',  True,  None),
    ('odysee',          'Odysee',          'odysee',          False, 'EF1970',  False, None),
    ('bilibili',        'Bilibili',        'bilibili',        False, '00A1D6',  False, None),

    # ---- Community / messaging -------------------------------------------
    ('slack',           'Slack',           'slack',           False, '4A154B',  False, None),
    ('vk',              'VK',              'vk',              False, '0077FF',  False, None),
    ('odnoklassniki',   'OK.ru',           'odnoklassniki',   False, 'EE8208',  False, None),
    ('kakaotalk',       'KakaoTalk',       'kakaotalk',       False, 'FFCD00',  True,  None),
    ('line',            'LINE',            'line',            False, '00C300',  False, None),
    ('quora',           'Quora',           'quora',           False, 'B92B27',  False, None),
    ('nextdoor',        'Nextdoor',        'nextdoor',        False, '6EAA0F',  False, None),

    # ---- Publishing, blogging & media ------------------------------------
    ('medium',          'Medium',          'medium',          False, '000000',  False, None),
    ('substack',        'Substack',        'substack',        False, 'FF6719',  False, None),
    ('wordpress',       'WordPress',       'wordpress',       True, '21759B',  False, None),
    ('hashnode',        'Hashnode',        'hashnode',        False, '2962FF',  False, None),
    ('devto',           'DEV',             'devto',           False, '0A0A0A',  False, None),
    ('listmonk',        'Listmonk',        'listmonk',        False, '0055D4',  False, None),
    ('tumblr',          'Tumblr',          'tumblr',          True, '36465D',  False, None),
    ('dribbble',        'Dribbble',        'dribbble',        False, 'EA4C89',  False, None),
    ('behance',         'Behance',         'behance',         False, '1769FF',  False, None),
    ('flickr',          'Flickr',          'flickr',          False, '0063DC',  False, None),
    ('yelp',            'Yelp',            'yelp',            False, 'D32323',  False, None),
    ('tripadvisor',     'Tripadvisor',     'tripadvisor',     False, '34E0A1',  True,  None),
    ('naver',           'Naver',           'naver',           False, '03C75A',  False, None),
    ('tistory',         'Tistory',         'tistory',         False, '2B2D33',  False, None),
    ('sinaweibo',       'Weibo',           'sinaweibo',       False, 'E6162D',  False, None),
    ('xiaohongshu',     'Xiaohongshu',     'xiaohongshu',     False, 'FF2442',  False, None),

    # ---- Creator commerce / web3 -----------------------------------------
    ('skool',           'Skool',           None,              False, '111114',  False, 'S'),
    ('whop',            'Whop',            None,              False, '111114',  False, 'W'),
    ('warpcast',        'Warpcast',        'farcaster',       False, '7C3AED',  False, None),
]

SVG_RE = re.compile(r'<svg[^>]*>(.*)</svg>', re.S)
TAG_RE = re.compile(r'<(?:path|circle|rect|polygon|line)\b[^>]*/?>', re.S)


def symbol_for(slug, si_file, letter, si_dir):
    if si_file:
        path = os.path.join(si_dir, si_file + '.svg')
        raw = open(path, encoding='utf-8').read()
        inner = SVG_RE.search(raw).group(1)
        inner = re.sub(r'<title>.*?</title>', '', inner, flags=re.S)
        tags = TAG_RE.findall(inner)
        body = ''.join(tags)
        if not body:
            raise SystemExit(f'no path data in {path}')
    else:
        body = (f'<text x="12" y="12" dy="0.36em" text-anchor="middle" '
                f'font-family="Arial, Helvetica, sans-serif" font-weight="700" font-size="11.5" '
                f'fill="currentColor" stroke="none">{letter}</text>')
    return (f'<symbol id="i-{slug}" viewBox="0 0 24 24" fill="currentColor" stroke="none">{body}</symbol>')


def main(si_dir):
    symbols = ['<?php // AUTO-GENERATED by tools/build_platforms.py — do not edit by hand. ?>',
               '<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">']
    css = ['/* AUTO-GENERATED by tools/build_platforms.py — do not edit by hand. */',
           '/* Platform brand tiles */',
           '/* Glyphs always take the tile color, never inherited card text (e.g. green status) */',
           'span.platform-avatar, span.platform-chip, span.matrix-tile, span.integration-icon { color: #fff; }',
           '.platform-avatar--instagram, .platform-chip--instagram {\n'
           '    background: linear-gradient(45deg, #feda75, #fa7e1e 25%, #d62976 60%, #4f5bd5);\n}']
    config = ["<?php",
              '// AUTO-GENERATED by tools/build_platforms.py — single source of truth for platforms.',
              'return [']

    for slug, name, si_file, available, hexv, dark_glyph, letter in PLATFORMS:
        if si_file:
            symbols.append(symbol_for(slug, si_file, letter, si_dir))
        else:
            symbols.append(symbol_for(slug, None, letter, si_dir))

        sel = f'.platform-avatar--{slug}, .platform-chip--{slug}, .matrix-tile--{slug}'
        if slug != 'instagram':
            css.append(f'{sel} {{ background: #{hexv}; }}')
        if dark_glyph:
            css.append(f'.platform-avatar.platform-avatar--{slug}, .platform-chip.platform-chip--{slug}, '
                       f'.matrix-tile.matrix-tile--{slug}, .integration-icon.platform-avatar--{slug} '
                       f'{{ color: #101114; }}')

        color = '#E1306C' if slug == 'instagram' else '#' + hexv
        config.append(f"    ['slug' => '{slug}', 'name' => '{name}', 'color' => '{color}', "
                      f"'available' => {'true' if available else 'false'}],")

    symbols.append('</svg>')
    config.append('];')

    out_sprite = os.path.join(REPO, 'views/partials/icons-platforms.php')
    out_css = os.path.join(REPO, 'public/assets/css/platforms.css')
    out_cfg = os.path.join(REPO, 'config/platforms.generated.php')
    open(out_sprite, 'w', encoding='utf-8').write('\n'.join(symbols) + '\n')
    open(out_css, 'w', encoding='utf-8').write('\n'.join(css) + '\n')
    open(out_cfg, 'w', encoding='utf-8').write('\n'.join(config) + '\n')

    n_avail = sum(1 for p in PLATFORMS if p[3])
    print(f'wrote {len(PLATFORMS)} platforms ({n_avail} available) to\n  {out_sprite}\n  {out_css}\n  {out_cfg}')


if __name__ == '__main__':
    main(sys.argv[1] if len(sys.argv) > 1 else os.path.join(REPO, 'tools/sources/si'))
