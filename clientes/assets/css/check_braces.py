
filename = r"c:\xampp\htdocs\consulteplus\assets\css\custom-sidebar.css"

with open(filename, 'r', encoding='utf-8') as f:
    lines = f.readlines()

open_braces = 0
for i, line in enumerate(lines):
    line_clean = line.split("/*")[0] # Ignore comments roughly
    open_braces += line_clean.count('{')
    open_braces -= line_clean.count('}')
    if open_braces < 0:
        print(f"Error: Negative brace count at line {i+1}")
        break

print(f"Final brace count: {open_braces}")
if open_braces == 0:
    print("Braces are balanced.")
else:
    print("Braces are UNBALANCED.")
