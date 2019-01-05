# Beevrr
Practice Laravel CRUD app. Discussions, or "propositions", are introduced, other users vote on them, and a winner is determined based on which opinion has a higher percentage-point change. Users who vote during the `pre-argument` phase cannot respond during the `argument` phase. Only users who voted during the `pre-argument` phase can vote during the `post-argument` phase. Based on the ["Oxford-Style" of debate](https://en.wikipedia.org/wiki/Debate#Oxford-style_debating).
## Usage
* Run `php artisan migrate`.
* Uses [beevrr-cron](https://www.github.com/01mu/beevrr-cron) for phase transitions.
