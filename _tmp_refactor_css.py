import re
from pathlib import Path

css_path = Path('c:/wamp64/dev/salas/app/public/css/estilo.css')
text = css_path.read_text(encoding='utf-8')

mapping = {
    '#000': 'text',
    '#222': 'overlay',
    '#2b2b2b': 'menuBg',
    '#2c73a8': 'primary',
    '#333': 'textStrong',
    '#333333': 'textStrong',
    '#3b3b3b': 'menuHoverText',
    '#444': 'textStrong',
    '#464646': 'menuBg',
    '#4e4e4e': 'text',
    '#575757': 'menuItem',
    '#666': 'textMuted',
    '#666666': 'textMuted',
    '#7aa9dd': 'primaryLight',
    '#888': 'textMuted',
    '#969696': 'borderStrong',
    '#999': 'borderStrong',
    '#9b1c1c': 'dangerDark',
    '#ccc': 'border',
    '#cccccc': 'border',
    '#d4d4d4': 'menuHoverBg',
    '#d4f7c4': 'success',
    '#dfdfdf': 'border',
    '#e1e1e1': 'border',
    '#e6e6e6': 'border',
    '#ececec': 'surfaceAlt',
    '#f0f0f0': 'surfaceAlt',
    '#f7f7f7': 'surface',
    '#fafafa': 'surfaceAlt',
    '#fbfabb': 'warning',
    '#fcc5c5': 'danger',
    '#f2c6c6': 'dangerBorder',
    '#ffecec': 'dangerLight',
    '#fff': 'bg',
    '#ffffff': 'bg',
    '#e0e0e0': 'border',
    '#eee': 'surfaceAlt',
}

def repl(match):
    hexv = match.group(0)
    key = hexv.lower()
    if key in mapping:
        token = mapping[key]
        return 'var(--color-' + token + ', ' + hexv + ')'
    return hexv

# Replace standalone hex colors
text = re.sub(r'(?<![0-9A-Fa-f])#(?:[0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})(?![0-9A-Fa-f])', repl, text)

# Convert font-size: Npx (and !important) to rem based on 14px base
def font_repl(match):
    px = int(match.group(1))
    rem = px / 14.0
    rem_str = ('%.4f' % rem).rstrip('0').rstrip('.')
    important = match.group(2) or ''
    return 'font-size: ' + rem_str + 'rem' + important

text = re.sub(r'font-size:\s*(\d+)px(\s*!important)?', font_repl, text)

css_path.write_text(text, encoding='utf-8')
print('done')
