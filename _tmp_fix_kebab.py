import re
from pathlib import Path

p = Path('c:/wamp64/dev/salas/app/public/css/estilo.css')
s = p.read_text(encoding='utf-8')

def kebab(m):
    name = m.group(1)
    # insert hyphen before uppercase letters
    k = re.sub(r'([a-z])([A-Z])', r'\1-\2', name).lower()
    return '--color-' + k

s = re.sub(r'--color-([A-Za-z]+)', kebab, s)
p.write_text(s, encoding='utf-8')
print('done')
