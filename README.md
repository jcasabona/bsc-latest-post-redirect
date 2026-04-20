=== Latest Post Redirect ===
Contributors: jcasabona
Tags: redirect
Donate link: https://paypal.me/jcasabona
License: GPL

A plugin that creates a redirect at `/latest` and sends users to the most recently published blog post.

The redirect uses HTTP 302 (temporary) because the target post changes whenever a newer post is published; a 301 would be cached by browsers and intermediaries and could strand visitors on an older post.

== Category-specific redirects ==
You can scope the redirect to a category:

- `/latest/articles` → latest post in the `articles` category
- `/latest/episodes` → latest post in the `episodes` category
- `/latest?category=articles` → same, via query string

By default only `articles` and `episodes` are allowed; any other slug falls through to a normal 404. Use the `bsc_latest_allowed_categories` filter to change the allowed list.

== Filters ==
`bsc_latest_orderby` — return `'modified'` instead of `'date'` to redirect to the most recently *modified* post.

`bsc_latest_allowed_categories` — array of category slugs permitted after `/latest/`. Defaults to `array( 'articles', 'episodes' )`.