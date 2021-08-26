# Private plugins in PHP

## Introduction

Did you know that you can visit unhosted static web applications using the [data URI scheme](https://en.wikipedia.org/wiki/Data_URI_scheme)? Inspired by this, I raised the question whether something like this could be possible for dynamic web content as well.

This is a proof of concept project to showcase that you can run various applications or plugins on the server side without requiring you to install each application on the server.

This is achieved by a very short, almost trivial script that processes requests on the server and an encoding that encrypts and signs your plugin in URI parameters. You can share such a link, endpoint or web form with others and they could use the same application.

### Use cases

#### Data at rest

The main threat model involves minimally protecting [data at rest](https://en.wikipedia.org/wiki/Data_at_rest) with low effort, i.e., against opportunistic hardware theft or in case of replacing a drive that had been deemed (partially) failed. This concerns both intellectual property and sensitive user data.
* Note that if this was the only use case, it would be good enough just to pass in a symmetric encryption key in a POST parameter, not the code itself.

#### Transparency for FOSS

In the spirit of the free (libre) software movement, a version could be implemented where server side code is minimized as much as possible, and plugin code would get passed in signed, but unencrypted.

As a more extreme example, only a deep link to a signed git commit in a public source code repository would be passed with the request. The server could build and cache the source code itself as well as the visitors of the website. They could also choose to run the version of the code they wish, as long as it is not on the blocklist. Extra care must be exercised to ensure database schema compatibility.

#### Subdivision of hosting

You and some of your close developer friends who somewhat trust each other may decide to share a single large hosting account.

* The account holder would run a dispatcher of low complexity similar to this one.
  * A kind of naive, good faith "sandboxing" could be set up, but nothing extensive.
  * For simple use cases, maybe a reduced subset of PHP could suffice that would be easy to validate using the tokenizer and enforcing the usage of a few new abstractions.
  * Full sanitization or an interpreter could also be implemented later on.
* Each developer would provide their public key to the account holder for addition to the signature checking allowlist.
* Plugins would be signed, unencrypted and sent in as URI query parameters that will be logged.
  * Logs can be analyzed to find out previously used plugins for investigations regarding abuse reports by third parties.
  * Ideally, they should be FOSS and these friends should review each other's plugins together continuously. Maybe a kind of leaderboard stats could also be displayed on the main page visible to all, so that the heaviest plugins will naturally attract more attention.
  * Data for website visitors and its corresponding encryption key may still be sent in the POST body. This is so that during open plugin code reviews, assuming good faith, participants would not need to sign an NDA.
* Access and error logs could be filtered by developer and provided to them for debugging.
* Optionally, it could be enforced that all website visitors register and sign all their requests (including the time or a developer payload sequence counter and their remote IP address). This might be used later on to prove that a certain payload was not only created by the developer, but it was executed as well on the shared server and for how many times.

#### Worms

To reduce the potential of malware infection on your shared host spreading to your site by way of code paths for automated scanning of known packages and libraries on the file system.

#### Snooping by host

To avoid opportunistic snooping and automated scanning by your host.

#### Function as a service

Some cool futuristic scenario where you can offload and distribute part of your interactive edge computation or networking demands to your server(s).

* https://en.wikipedia.org/wiki/Function_as_a_service
* https://en.wikipedia.org/wiki/Serverless_computing

#### User managed plugins

As a system operator, you may have installed some complicated software, but didn't bother to fine-tune it or install all available plugins, or catch up with the ever increasing number of official plugins being developed for it. You can allow your users to make use of new plugins that are signed by the original software developer (or you, or your web of trust) without you having to install and maintain each of them individually on your server.

* Outdated vulnerable plugins could potentially be disabled worldwide at the same time by rotating keys or automatically keeping a revision blocklist or allowlist in sync.
* If for some reason, your disk quota is too low to host each and every possible software component that sees very low utilization in the long tail of the distribution. As many normal page loads today weigh in the megabytes, it would not be an unrealistic burden to attach 100kB of code to a request that is capable of serving an aggregate of rich responses, perhaps with increasing efficiency due to customization.
* Note that this is not the best solution to cope with the mentioned scenario, but still demonstrates an entertaining alternative, nonetheless.

### Drawbacks

#### Extra bandwidth

The compressed plugin needs to be uploaded with each request.

* Usually not high for short code snippets in scope of this project.
* A stateful or customized alternative could be constructed where only the encryption key and/or the ID needs to accompany requests.

#### Decryption overhead

The runtime overhead is not very high with today's CPU acceleration.

#### Signature verification

Public key cryptography based signature verification can take some time depending on the chosen algorithm and the size of the key.

* Could be optimized later on to only run when first encountering a given plugin.

#### JIT

Opcode cache and probably JIT aren't applicable for runtime constructed source code.

* Unpacking plugins to `tmpfs` could mitigate this.

## Installation

Run this on your developer machine equipped with PHP7+ and the OpenSSL extension _(enabled by default)_:

* `php publish.php`
* Creates secret keys in `var` that you should protect
* Generates `deploy/index.php` that you should **transfer to your public server** _once_ after keys are (re)created
* Packs [example/wiki.php](example/wiki.php) into a URI that will be ran by your server every time you click on it

If you use the resulting parameter in an HTTP GET for testing (like in an `<img src=...?p=...>`), it will reveal your key in the access logs of the server! So instead, you should do an HTTP POST with the encrypted code using either:

* a `<form>` as seen in [example/wiki.php](example/wiki.php)
* the trampoline handling the hidden fragment at the end `<a href=...#...>` if you are targeting (JavaScript capable) web browsers
* AJAX or `curl -d p=...` directly

You should host all sources containing this code from a separate, possibly static web server. It does not make sense to store the encryption key next to the encrypted data itself, revealing both at the same time in case of a compromise.

## Road map

* There is lot of potential that could be researched, but I'll leave it to the audience to decide which should be the next step.
* Elliptic curve cryptography would enable using a shorter signature and lead to more efficient verification
* The signature could serve as a seed for the initialization vector.
* [Perfect forward secrecy](https://en.wikipedia.org/wiki/Forward_secrecy#Protocols): can possibly be approximated when communicating using JavaScript by splitting in two consecutive HTTP requests and connecting them via a session cookie or some other token
  * Only needed if one of your nodes is not HTTPS-enabled.
* For increased protection, material stored in the database or accessed from another web server (potentially also via PFS) could be mixed into the key material before use.
* Enable caching of large plugins on tmpfs perhaps encrypted with a different key to be provided along with thin variants of requests so that the majority of requests would not need to send and verify the same plugin repeatedly. The decrypted plugin would still only be accessible for the duration of processing a request.
* Using a compression dictionary can improve compression ratio a lot for small input.
  * Just consider prepending the handler source already existing on the server.
  * Could be seeded with PHP and HTML tokens and some common constructs.
  * As a riskier improvement, prepend the source of a selected PHP library and/or a natural language wordlist that is already installed on the server. This will break after each update, so care must be taken to notify the developer when it breaks (via email or RSS monitoring).
* brotli compresses much better than gzip even for so tiny inputs
* A plugin could be automatically stripped during publishing to reduce size
  * https://www.php.net/manual/en/tokenizer.examples.php
  * https://github.com/nikic/PHP-Parser
  * https://github.com/box-project/box/blob/master/doc/configuration.md#compactors-compactors
* Support efficient attaching of binary resources without resorting to double encoding. Can be useful for serving a favicon or a downloadable archive.

### Usable character set for more efficient encoding

`[?][A-Za-z0-9._~!$&'()*+,;=:@/?-]*`

* Theoretical character set size: 81, 6.34 bit/byte
* Reduction compared to base-64: -5%
* Chromium and Firefox
  * Disallowed by standard: `[][\u0000-\u001f "#%<>\\^`{|}]`
  * Encodes: `[']`
  * Maintains: ```[][\\^`{|}]```
  * Overall 6.46 bit/byte
* References
  * https://www.rfc-editor.org/rfc/rfc3986.html#section-3.4
  * https://en.wikipedia.org/wiki/Uniform_Resource_Identifier#Syntax
  * https://en.wikipedia.org/wiki/Percent-encoding#The_application/x-www-form-urlencoded_type
* If using AJAX, binary could be sent directly.

### Related concepts

* Layer confidentiality and integrity on top of a non-SSL connection using JavaScript when transferring user controlled parameters or results to/from such a server using public key cryptography and signatures
* Storing data in the database encrypted and signed with a key available on the file system
  * These two should reside on two separate machines
  * See [peppering](https://en.wikipedia.org/wiki/Pepper_(cryptography))
  * Ideally, where available, use a tmpfs mounted location that gets lost on power failure and needs to get reloaded after every boot, like `/dev/shm`, `/run/lock` or `/run/user/$UID`
* TODO: can some kind of a scheme for privacy or integrity be devised using `.htaccess` and SSI for static web servers?

## Copyright

* [LICENSE](LICENSE)
