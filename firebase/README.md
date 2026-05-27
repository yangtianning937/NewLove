# Firebase Migration Notes

This folder prepares the NewLove project for a future Firebase Firestore migration.

The current CakePHP application still uses MySQL through CakePHP ORM. Moving the whole application to Firebase is not a one-line configuration change because the controllers and table classes currently depend on SQL tables, joins, and CakePHP entities.

## Current Migration Step

The current MySQL data has been exported into Firestore-friendly JSON files:

```text
firebase/firestore-data/
```

Each MySQL table is exported as one JSON file. Each item uses this shape:

```json
{
  "document_id": "1",
  "data": {
    "id": 1,
    "name": "Example"
  }
}
```

The `document_id` can be used as the Firestore document id. The `data` object can be stored as the document data.

## Export Again

Run this command from the project root:

```bash
DB_NAME=newLove DB_USER=newLove DB_PASS=password /opt/homebrew/opt/php@8.3/bin/php scripts/export_firestore_json.php
```

## Firebase Project

The matching Firebase project for this app is:

```text
newlove-yt-260528
```

The Firestore database uses Native mode in the `australia-southeast1` region.

## Import Into Firestore

Run this command from the project root after getting a Google OAuth access token:

```bash
FIREBASE_PROJECT_ID=newlove-yt-260528 FIREBASE_ACCESS_TOKEN=your_access_token /opt/homebrew/opt/php@8.3/bin/php scripts/import_firestore_json.php
```

The import script reads `firebase/firestore-data/manifest.json` and writes each JSON file into a Firestore collection with the same name.

## Suggested Firestore Collections

- `users`
- `products`
- `collections`
- `colours`
- `rawmaterials`
- `suppliers`
- `product_inventories`
- `rawmaterial_inventories`
- `materials_products`

## Recommended Next Step

After the data is in Firestore, migrate one module at a time. A safe order is:

1. Read-only product list
2. Read-only raw material list
3. Supplier list
4. Create/edit/delete operations
5. Authentication migration

This keeps the project usable while Firebase support is added gradually.
