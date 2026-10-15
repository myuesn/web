# WLF AGRIPARTS

An English/Turkish B2B agricultural spare-parts enquiry website. No prices or checkout are displayed. Categories are illustrative enquiry categories; they are not a verified stock catalogue.

## Run

No dependency installation or build is required. Use Python 3:

```sh
cd /workspace/web
python3 -m http.server 3000 --bind 0.0.0.0
```

## Quotation enquiries

Visitors can filter categories, search in English or Turkish, add categories to an enquiry, and enter company, destination, machine and quantity details. The email action opens their email client with a draft addressed to `sales@wlfagriparts.com`. The visitor must send it. The download action creates a plain-text enquiry as a fallback. No backend receives or stores form data; delivery has not been tested. Large enquiries may exceed email-client URL limits; use the downloaded file in that case.

## Before launch

Confirm the WLF AGRIPARTS brand name, supply actual product references and photos, and verify mailbox delivery. Add company/legal information appropriate to the business. Host these static files on your chosen HTTPS hosting service and configure the domain separately. The included SVG artwork is illustrative. No third-party runtime assets are required.

## Validation

Validated in Chromium: category filtering, enquiry selection, English/Turkish switching, search and empty state, generated enquiry file contents, and no horizontal overflow at 375px width. `node --check app.js` passes. Actual outgoing email requires a configured email client and mailbox verification.
