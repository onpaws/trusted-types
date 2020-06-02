# Trusted Types Experiments

### TL, DR
What's this? A starting Trusted Types + CSP config intended for WordPress running on Apache

### How do I use it? (Quick version)
1. Add a Content Security Policy to your webserver, with a Trusted Types section (example below).
2. Clone https://github.com/onpaws/trusted-types
3. Build:
    ```
    cd trusted-types
    npm run build
    ```
4. Upload `lib/trustedTypes.js` to your server, and inside `index.html` or equivalent and load trustedTypes.js as early as possible.


### Who is this for?
Web developers roughly familiar with security best practices like [CSP](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy) and [SameSite](https://web.dev/samesite-cookies-explained/), who might be considering taking their security stance to the next level courtesy of the new Trusted Types API.

### Why?
Recently Chrome started supporting [Trusted Types](https://w3c.github.io/webappsec-feature-policy/) by [default](https://www.chromestatus.com/feature/5650088592408576). The [cross browser story](https://caniuse.com/#search=trustedtype) is still early but Mozilla may be making [some](https://github.com/mozilla/standards-positions/issues/20) [moves](https://github.com/mozilla/standards-positions/commit/e47ddba3948504fb08f708d9555cb8f1cca26c4f) also.

Additional evidence Google appears to be 'all in' on this: I noticed the [CSP evaluator](https://csp-evaluator.withgoogle.com/) at the time of writing includes a Trusted Type clause (`require-trusted-types-for 'script'`) in the 'Sample safe policy'. That wasn't there last time I checked.

### How the heck do I use Trusted Types? (Detailed version)
In some ways, namely living inside the browser, it's similar to CORS & CSP. It's is all about restricting who can use certain DOM APIs, and is intended to help prevent DOM XSS attacks speficially. 
By carefully whitelisting the things you want/expect, everything else can be blocked by default.
Basically carefully analyze places your app uses e.g. `.innerHTML`,  `<script src>`, `document.write` and friends. Either remove it, or add it to a whitelist.

I haven't seen so many copypastable examples of how this is supposed to work, so this repo is intended to share what I've learned in standing this up for my personal site. With any luck other you may be able to use this to address more XSS attack vectors in your app.

First, setup a CSP header in your webserver. 

```
Content-Security-Policy "default-src 'none'; ...rest of your policy goes here
```

Next add the Trusted Types expressions to your policy

```
require-trusted-types-for 'script';\
trusted-types default;\
```
(you can have as many policies as you want; use whatever names(s) you like)

Next, you will include a small amount of JavaScript, a single file is probably fine, that should load early on -- before whatever other JS in your app makes calls that assigns to a 'source' to a 'sink'. For example, element.innerHTML = '<someHTML></someHTML>...'

Real websites will probably have at least some features that will need to be whitelisted, which is what ALLOWED_HTML and ALLOWED_SCRIPTS_REGEXP is for.

Idiomatic React and Angular SPAs should in many cases have a limited amount of places the DOM is mutated, so hopefully this won't be too painful.

While it's early days, hopefully in the future these files should be able to be the basis for compile time type checking.

### Extra gory details for WordPress users
What makes WordPress a challenge is that there are many snippets that mutate the DOM, which means special handling to stay compliant with CSP/Trusted Types. The WP admin page in particular looks like it will take the WordPress team some effort to update. 
Anyway after a bit of careful disentangling, I was able to update [twentytwenty](https://wordpress.org/themes/twentytwenty/) aka the latest-gen 'official' WordPress theme, to work with a strict CSP policy + Trusted Types.

This repo contains that policy and the ancillary files I needed to craft for my particular hosting environment.

### Caveats 
I make no guarantees on this. It's worked for me on Mac using Chrome, Firefox and Safari. I've tested zero times with IE.