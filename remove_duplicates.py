import re

with open(r'C:\OSPanel\home\Codemaster\database\seeders\RoadmapContentSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Remove quiz entries that use array() syntax (old duplicate questions)
# Pattern matches lines like: ['question' => '...', 'options' => array (...), 'correct' => '...'],
pattern = r"(?m)^\s*\['question'\s*=>\s*'.*?'options'\s*=>\s*array\s*\([^)]*\)[^]]*\]\s*,?\s*\n"
content = re.sub(pattern, '', content)

with open(r'C:\OSPanel\home\Codemaster\database\seeders\RoadmapContentSeeder.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Done. Removed duplicate array() quiz entries.")
