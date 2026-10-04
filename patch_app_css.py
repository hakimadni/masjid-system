import re

with open('/Users/user/Work/masjid-system/resources/css/app.css', 'r') as f:
    content = f.read()

# Let's replace the body background with a more character-rich Islamic pattern and a soft emerald tint
old_body = """body {
    @apply min-h-screen bg-[#f4f7f2] text-slate-900 antialiased;
    background-image:
      radial-gradient(circle at top left, rgba(34, 197, 94, 0.08), transparent 32%),
      linear-gradient(180deg, rgba(255, 255, 255, 0.85), rgba(244, 247, 242, 0.96));
    background-attachment: fixed;
  }"""

new_body = """body {
    @apply min-h-screen bg-[#f8faf7] text-slate-800 antialiased;
    background-image:
      linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 247, 0.98)),
      url("https://www.transparenttextures.com/patterns/arabesque.png");
    background-attachment: fixed;
    font-feature-settings: "cv02", "cv03", "cv04", "cv11";
  }"""

content = content.replace(old_body, new_body)

# Update some root variables to be slightly richer
content = content.replace("--primary: 142 52% 45%;", "--primary: 160 84% 39%; /* Deep Emerald */")
content = content.replace("--ring: 142 44% 48%;", "--ring: 160 84% 39%;")
content = content.replace("--border: 140 12% 87%;", "--border: 150 15% 90%;")

with open('/Users/user/Work/masjid-system/resources/css/app.css', 'w') as f:
    f.write(content)

