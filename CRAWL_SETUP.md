# Crawl API Setup Guide for Mira AI Training

## Problem
The hosting provider (free.nf) has anti-bot protection that blocks scrapers like Mira AI from accessing the endpoint directly.

**Solution:** Use a tunnel (ngrok or cloudflared) to expose your local dev server without anti-bot.

---

## Quick Setup

### Step 1: Start Local PHP Server
Open PowerShell and run:
```powershell
cd "C:\Users\quock\OneDrive\Máy tính\TTCN\bookstore_customer"
php -S 127.0.0.1:3000
```
You should see:
```
[Mon Dec 28 12:00:00 2025] PHP 8.x.x Development Server is running
[Mon Dec 28 12:00:00 2025] Listening on http://127.0.0.1:3000
```

**Verify locally:**
- Open browser: http://127.0.0.1:3000/crawl/products
- Should see JSON response (or blank if DB has no products)

### Step 2: Create Public URL with ngrok
Download ngrok: https://ngrok.com/download

Install and add to PATH, then run:
```powershell
ngrok http 3000
```

You'll see output like:
```
Session Status                online
Version                       3.x.x
Region                        us (United States)
Forwarding                    https://abc123def456.ngrok.io -> http://localhost:3000
Web Interface                 http://127.0.0.1:4040
```

**Copy the HTTPS URL** (e.g., `https://abc123def456.ngrok.io`)

### Step 3: Share to Mira AI
Give Mira AI one of these URLs:

**Option A: JSON (default)**
```
https://YOUR-NGROK-ID.ngrok.io/crawl/products
```

**Option B: HTML with schema.org microdata**
```
https://YOUR-NGROK-ID.ngrok.io/crawl/products?format=html
```

**Option C: Plain text (one product per line)**
```
https://YOUR-NGROK-ID.ngrok.io/crawl/products?format=text
```

**Option D: Rich training feed (recommended)**
```
https://YOUR-NGROK-ID.ngrok.io/public/bot_training/products_data.php
```
This includes: categories, authors, publishers, promotions, reviews, final prices, stock status.

---

## Sample JSON Response

```json
{
  "success": true,
  "count": 3,
  "total": 3,
  "page": null,
  "per_page": null,
  "items": [
    {
      "id": 1,
      "type": "book",
      "name": "Product Name",
      "description": "Description here",
      "price": 150000.00,
      "stock": 10,
      "sold": 5,
      "category": "Category Name",
      "author": "Author Name",
      "publisher": "Publisher Name",
      "provider": "Provider Name",
      "image_url": "https://YOUR-NGROK-ID.ngrok.io/bookstore_customer/assets/images/products/image.jpg",
      "url": "https://YOUR-NGROK-ID.ngrok.io/bookstore_customer/index.php?page=productview&id=1"
    }
  ]
}
```

---

## Query Parameters

Add to the URL:
- `?p=2` – Get page 2
- `?per_page=50` – Limit items per page
- `?limit=100` – Hard cap on total items returned
- `?format=html` – Get HTML with schema.org markup instead of JSON
- `?format=text` – Get plain text (name | price | category | url)

**Example:**
```
https://YOUR-NGROK-ID.ngrok.io/crawl/products?p=1&per_page=50&format=html
```

---

## Alternative: Cloudflared Tunnel

If ngrok doesn't work for you, use cloudflared (Cloudflare):

```powershell
# Install cloudflared or download from https://developers.cloudflare.com/cloudflare-one/connections/connect-applications/install-and-setup/installation/
cloudflared tunnel --url http://127.0.0.1:3000
```

You'll get a public HTTPS URL like: `https://your-url.trycloudflare.com`

---

## Troubleshooting

**Q: "Cannot GET /crawl/products"**
- Ensure the PHP server is running (see Step 1)
- Check the URL is correct (no typos)

**Q: JSON is empty or "success": false**
- The database might be unreachable
- Check PHP errors in the server terminal
- Verify database credentials in `app/config.php`

**Q: ngrok says "invalid tunneling protocol"**
- Make sure PHP server is running on 127.0.0.1:3000 first
- Restart ngrok: `ngrok http 3000`

**Q: Mira AI still can't scrape**
- Try the HTML format: `…/crawl/products?format=html`
- Try the rich feed: `…/public/bot_training/products_data.php`
- Check ngrok web interface at http://127.0.0.1:4040 to see requests

---

## For Production (Optional)

Once Mira AI training is done, consider:
- Migrate to a proper hosting with CDN (not free.nf)
- Add caching headers to reduce load
- Implement API rate limiting
- Use a dedicated subdomain for the API

---

**Questions?** Check if your local server is running first, then verify ngrok is connecting properly.
