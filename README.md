# private-plugin.ph

## Introduction

### Use cases

* The main threat model involves minimally protecting [data at rest](https://en.wikipedia.org/wiki/Data_at_rest) with low effort, i.e., against opportunistic hardware theft or in case of replacing a drive that had been deemed (partially) failed. This involves both intellectual property and sensitive user data.
  * Note that if this was the only use case, it would be good enough just to pass in a symmetric encryption key in a POST parameter.
* To reduce the potential of malware infection on your shared host spreading to your site by way of code paths for automated scanning of known packages and libraries on the file system.
* To avoid opportunistic snooping and automated scanning by your host.
* Some cool futuristic scenario where you can offload part of your interactive edge computation or networking demands to your server(s).
* As a system operator, you may have installed some complicated software, but didn't bother to fine tune it or all install all plugins, or catch up with the ever increasing number of official plugins being developed for it. You can allow your users to make use of new plugins that are signed by the original software developer (or you, or your web of trust) without you having to install anything on your server.
  * Outdated vulnerable plugins could potentially be disabled worldwide at the same time by rotating keys or automatically keeping a revision blocklist or allowlist in sync.
  * Note that this is not the best solution to cope with the scenario, but still demonstrates an entertaining alternative, nonetheless.

## Install

Run this on your developer machine equipped with PHP7+ having the OpenSSL extension (enabled by default):

* `php [publish.php](publish.php)`
* Creates secret keys in `var` that you should protect
* Generates `deploy/index.php` that you should **transfer to your public server** _once_ after keys are (re)created
* Packs [examples/wiki.php](examples/wiki.php) into a URI that will be ran by your server every time you click on it

If you use the URI in an HTTP GET for testing (like in an `<img>` or `<a href=...>`), it will reveal your key in the access logs of the server! So you should do an HTTP POST with the encrypted code using a `<form>` (or via AJAX) as seen in [examples/wiki.php](examples/wiki.php) instead.

You should host all sources containing this code from a separate, possibly static web server. It does not make sense to store the encryption key next to the encrypted data itself, revealing both at the same time in case of a compromise.

## Roadmap

* Elliptic curve cryptography would enable using a shorter signature
* [perfect forward secrecy](https://en.wikipedia.org/wiki/Forward_secrecy#Protocols): can possibly be approximated when communicating using JavaScript by splitting in two consecutive HTTP requests and connecting them via a session cookie or some other token
* For increased protection, material stored in the database or accessed from another web server (via PFS) could be mixed into the key material before use.

### Related concepts

* Layer confidentiality and integrity on top of a non-SSL connection using JavaScript when transferring user controlled parameters or results to/from such a server using public key cryptography and signatures
* Storing data in the database encrypted and signed with a key stored on the file system
  * A little like [peppering](https://en.wikipedia.org/wiki/Pepper_(cryptography))
  * Ideally where available, at a tmpfs mounted location that gets lost on power failure and needs to get reloaded after every boot, like `/dev/shm`, `/run/lock` or `/run/user/$UID`
  * Usually these two reside on two separate machines
* TODO: can some kind of a scheme for privacy or integrity be devised using `.htaccess` and SSI for static servers?

## Copyright

* [LICENSE](LICENSE)
