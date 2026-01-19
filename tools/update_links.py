#!/usr/bin/env python3
import os

def update_services_links(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Replace services.html links with about.html
        content = content.replace('href="services.html"', 'href="about.html"')
        content = content.replace('href="services.html#', 'href="about.html#')
        
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated: {file_path}")
            return True
        return False
    except Exception as e:
        print(f"Error processing {file_path}: {e}")
        return False

count = 0
for root, dirs, files in os.walk('.'):
    for file in files:
        if file.endswith('.html'):
            file_path = os.path.join(root, file)
            if update_services_links(file_path):
                count += 1

print(f"\nTotal files updated: {count}")
