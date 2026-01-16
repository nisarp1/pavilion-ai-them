import re
import os

def minify_css(input_path, output_path):
    try:
        with open(input_path, 'r') as f:
            css = f.read()

        # Remove comments
        css = re.sub(r'/\*[\s\S]*?\*/', '', css)
        # Remove whitespace around symbols
        css = re.sub(r'\s*([\{\}:;,])\s*', r'\1', css)
        # Remove trailing semicolons
        css = re.sub(r';}', '}', css)
        # Remove newlines and multiple spaces
        css = re.sub(r'\s+', ' ', css)
        
        with open(output_path, 'w') as f:
            f.write(css.strip())

        print(f"Minified {input_path} -> {output_path}")
        print(f"Original size: {os.path.getsize(input_path)} bytes")
        print(f"Minified size: {os.path.getsize(output_path)} bytes")
    except Exception as e:
        print(f"Error minifying {input_path}: {e}")

if __name__ == "__main__":
    minify_css('assets/css/style.css', 'assets/css/style.min.css')
