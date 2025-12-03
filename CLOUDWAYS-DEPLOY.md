# Hosting Pavilion Theme on Cloudways

This guide describes how to host the Pavilion Theme on Cloudways and configure it to use the production APIs.

## Prerequisites

- A Cloudways account with an active server.
- A PHP application created on Cloudways (Custom PHP or WordPress).
- Access to the application via SFTP or SSH.

## 1. Prepare the Code

The code has been configured to use the production API endpoints.

- **API URL**: `https://pavilion-ai-production.up.railway.app/api` (Configured in `core.php`)
- **Frontend URL**: `https://pavilion-ai.vercel.app/` (If applicable)

### Key Configuration Files

- `core.php`: Contains the main API configuration.
  ```php
  define('PAVILION_API_BASE_URL', 'https://pavilion-ai-production.up.railway.app/api');
  ```
- `assets/js/exchange-rates.js` & `assets/js/exchange-rates-simple.js`: Configured to use relative paths for the exchange rates script.

## 2. Deployment Methods

You can deploy using either Git (recommended) or SFTP.

### Option A: Deploy via Git (Recommended)

1.  **Initialize Git Repository** (Already done locally):
    The local directory is now a git repository.

2.  **Generate SSH Key on Cloudways**:
    - Go to **Server Management** > **Master Credentials** > **SSH Public Keys**.
    - If you don't have one, generate an SSH key on your local machine (`ssh-keygen -t rsa`) and add the public key to Cloudways.

3.  **Get Deployment URL**:
    - Go to **Application Management** > **Deployment Via Git**.
    - Click **Generate SSH Key** (if you want Cloudways to pull from a private repo) or skip if pushing directly.
    - **Note**: Cloudways "Deployment Via Git" is typically for pulling from a remote repo (GitHub/GitLab).

    **Alternative: Push directly to Cloudways (Production)**
    Cloudways doesn't host git repositories directly for pushing. The standard workflow is:
    
    **Local -> GitHub/GitLab -> Cloudways (via Deployment feature)**

    **Step-by-Step Guide:**
    
    1.  **Create a Repository on GitHub/GitLab**.
    2.  **Push your code**:
        ```bash
        git remote add origin https://github.com/nisarp1/pavilion-ai-them.git
        git push -u origin master
        ```
    3.  **Connect Cloudways to Repository**:
        - In Cloudways, go to **Application** > **Deployment Via Git**.
        - Enter your Repository URL: `https://github.com/nisarp1/pavilion-ai-them.git`
        - Authenticate (if private).
        - Select branch `master`.
        - Click **Start Deployment**.
    
    4.  **Auto-Deployment (Optional)**:
        - Enable "Auto Deployment" in Cloudways to automatically pull changes when you push to GitHub.

### Option B: Upload via SFTP

Upload the contents of the `pavilion-theme` directory to your Cloudways application's `public_html` folder.

**Exclude the following files/folders:**
- `.git`
- `.gitignore`
- `.vscode`
- `*.md`
- `test-*.php`

## 3. Server Configuration

Ensure your Cloudways server meets the following requirements:

- **PHP Version**: 7.4 or higher (8.0+ recommended).
- **Extensions**: `curl`, `json`, `mbstring` (Standard on Cloudways).

### Permissions

Ensure the files have standard permissions:
- Directories: `755`
- Files: `644`

## 4. Verify API Connection

Once uploaded, visit your site URL.

1.  Check the homepage. It should load articles from the Railway API.
2.  If you see errors or empty content:
    - Check the PHP error logs on Cloudways (`logs/php_error.log`).
    - Verify that the Cloudways server can reach `https://pavilion-ai-production.up.railway.app/`. You can test this via SSH:
      ```bash
      curl -I https://pavilion-ai-production.up.railway.app/api/articles/
      ```

## 5. Troubleshooting

- **CORS Errors**: If you see CORS errors in the browser console regarding the exchange rates, ensure `assets/scripts/exchange-rates.php` is accessible and returning JSON.
- **API Errors**: If `core.php` fails to fetch data, check if the Railway app is sleeping or down.

## 6. Vercel Integration

If you need to link to the Vercel frontend (`https://pavilion-ai.vercel.app/`), you can update the relevant links in the theme files (e.g., `header.php` or `footer.php`). Currently, the theme is set to run as a standalone PHP site consuming the Railway backend.
