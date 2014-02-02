Readme coming soon. Here's the basics:

* `/<slug>/` returns a list of arrays, all data from that game. Slugs are txt files in [app/data](https://github.com/blha303/p2sounds.blha303.com.au/tree/master/app/data), without .txt.
* `/<slug>/<id>` returns a single array, the info from that sound. IDs are from portal2sounds.com.
* `/search/<slug>/<term>` searches for sounds containing <term> in their transcription, and returns a list of arrays with the results.

You can specify format by adding either ?format=json or ?format=xml on the end of the URL, or add "application/json" or "text/xml" to the Accept header on your request.

With thanks to portal2sounds.com for collecting the data, and Valve for making these games.
