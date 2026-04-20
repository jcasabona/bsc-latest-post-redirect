=== Latest Post Redirect ===
Contributors: jcasabona
Tags: redirect
Donate link: https://paypal.me/jcasabona
License: GPL

A plugin that creates a redirect at `/latest` and sends users to the most recently published blog post.

The redirect uses HTTP 302 (temporary) because the target post changes whenever a newer post is published; a 301 would be cached by browsers and intermediaries and could strand visitors on an older post.

== Filters ==
`bsc_latest_orderby` — return `'modified'` instead of `'date'` to redirect to the most recently *modified* post.