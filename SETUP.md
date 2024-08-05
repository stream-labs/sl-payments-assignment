## Getting Started 

Clone the repository and checkout the `interview/chris-arter` branch.

1. Run `composer install`
2. Copy `.env.example` to `.env` and set the appropriate environment variables.
2. Run `docker compose up -d`
4. Generate an application key:

```bash
composer artisan key:generate
```
5. To see the table, run:

```bash
composer artisan report:stripe
```

You should see a table like this in the console:



### Running Stripe Commands
To run stripe commands in the container, you can use
the `composer stripe` script.

This allows you to not have to install the stripe CLI, and it's already configured based on the `.env` file.

```bash
composer stripe report:stripe
```

### Notes

There's some code in `/app/Actions` that was an attempt to automate the
clock advancement, but I kept getting race conditions.