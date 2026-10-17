# NPI Registry Search

A Laravel and Livewire application for searching healthcare providers in the
U.S. Centers for Medicare & Medicaid Services (CMS) National Provider Identifier
(NPI) Registry. Search results are retrieved from the NPI Registry API and
display provider names, NPI numbers, primary taxonomy, and practice addresses.
Select a result to view additional details, including practice and mailing
addresses, taxonomies, and other identifiers.

## Search options

Search by one or more of the following:

- First and last name
- 10-digit NPI number
- Taxonomy description
- City, state, and ZIP code

Results are paginated. Search requests with no criteria, or with only a state,
are not submitted.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- SQLite (the default database configuration)

## Installation and setup

1. Install PHP and JavaScript dependencies and build the frontend:

   ```sh
   composer run setup
   ```

   This installs Composer and npm dependencies, creates the application key,
   runs database migrations, and builds the frontend assets.

2. Configure the NPI Registry API and cache in the project-root `.env` file.
   These values are required:

   ```dotenv
   NPI_REGISTRY_URL=https://npiregistry.cms.hhs.gov/api/
   NPI_REGISTRY_VERSION=2.1
   NPI_REGISTRY_TIMEOUT=10
   CACHE_STORE=file
   ```

   `CACHE_STORE=file` uses local file-based caching for API calls. Successful
   NPI Registry responses are cached for one hour.

3. Start the application:

   ```sh
   composer run dev
   ```

## Technology

- Laravel
- Livewire
- Tailwind CSS and Vite
- CMS NPI Registry API