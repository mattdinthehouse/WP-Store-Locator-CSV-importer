## How it works
1. Parses the CSV into an array of key-value pairs for each row
2. For each row:
  1. It checks if a store already exists with that Account ID
  2. If one is found it will update that store, otherwise a new store is created
  3. The import timestamp (using PHP's time() function) and the row data is saved in meta fields on that store
  4. Location data is imported
  5. If the new location data doesn't match the old location data, the lat-long is geocoded
  6. Any additional information (phone, website, fax, etc) is imported
3. The number of stores imported is printed to the user, plus any errors

### Notes about how it works
Column names have non-printable characters stripped, underscores replaced with spaces, PHP's trim() is called, then WP's sanitize_title() is called.

Row values have PHP's trim() called.

The location geocode uses WP Store Locator's own geocoding function so it must be configured with a Google Maps key before running the importer.