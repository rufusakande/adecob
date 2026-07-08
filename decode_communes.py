import re
from collections import Counter

path = 'adecob_DB.sql'
regex = re.compile(r"INSERT INTO `infrastructures` .*?VALUES\s*\((.*?)\)\s*;", re.S)
communes = Counter()
with open(path, encoding='utf-8') as f:
    text = f.read()
for match in regex.finditer(text):
    row = match.group(1)
    vals = []
    cur = ''
    esc = False
    quote = None
    for c in row:
        if quote:
            if c == '\\' and not esc:
                esc = True
                cur += c
                continue
            if c == quote and not esc:
                quote = None
            else:
                esc = False
            cur += c
        else:
            if c in "'\"":
                quote = c
                cur += c
            elif c == ',':
                vals.append(cur.strip())
                cur = ''
            else:
                cur += c
    if cur:
        vals.append(cur.strip())
    if len(vals) >= 5:
        commune = vals[4].strip()
        if commune.startswith("'") and commune.endswith("'"):
            commune = commune[1:-1].replace("\\'", "'")
        communes[commune] += 1
print('unique communes:', len(communes))
for c, n in communes.most_common():
    print(n, c)
