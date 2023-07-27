/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/bad-words/lib/badwords.js":
/*!************************************************!*\
  !*** ./node_modules/bad-words/lib/badwords.js ***!
  \************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

const localList = __webpack_require__(/*! ./lang.json */ "./node_modules/bad-words/lib/lang.json").words;
const baseList = __webpack_require__(/*! badwords-list */ "./node_modules/badwords-list/lib/index.js").array;

class Filter {

  /**
   * Filter constructor.
   * @constructor
   * @param {object} options - Filter instance options
   * @param {boolean} options.emptyList - Instantiate filter with no blacklist
   * @param {array} options.list - Instantiate filter with custom list
   * @param {string} options.placeHolder - Character used to replace profane words.
   * @param {string} options.regex - Regular expression used to sanitize words before comparing them to blacklist.
   * @param {string} options.replaceRegex - Regular expression used to replace profane words with placeHolder.
   * @param {string} options.splitRegex - Regular expression used to split a string into words.
   */
  constructor(options = {}) {
    Object.assign(this, {
      list: options.emptyList && [] || Array.prototype.concat.apply(localList, [baseList, options.list || []]),
      exclude: options.exclude || [],
      splitRegex: options.splitRegex || /\b/,
      placeHolder: options.placeHolder || '*',
      regex: options.regex || /[^a-zA-Z0-9|\$|\@]|\^/g,
      replaceRegex: options.replaceRegex || /\w/g
    })
  }

  /**
   * Determine if a string contains profane language.
   * @param {string} string - String to evaluate for profanity.
   */
  isProfane(string) {
    return this.list
      .filter((word) => {
        const wordExp = new RegExp(`\\b${word.replace(/(\W)/g, '\\$1')}\\b`, 'gi');
        return !this.exclude.includes(word.toLowerCase()) && wordExp.test(string);
      })
      .length > 0 || false;
  }

  /**
   * Replace a word with placeHolder characters;
   * @param {string} string - String to replace.
   */
  replaceWord(string) {
    return string
      .replace(this.regex, '')
      .replace(this.replaceRegex, this.placeHolder);
  }

  /**
   * Evaluate a string for profanity and return an edited version.
   * @param {string} string - Sentence to filter.
   */
  clean(string) {
    return string.split(this.splitRegex).map((word) => {
      return this.isProfane(word) ? this.replaceWord(word) : word;
    }).join(this.splitRegex.exec(string)[0]);
  }

  /**
   * Add word(s) to blacklist filter / remove words from whitelist filter
   * @param {...string} word - Word(s) to add to blacklist
   */
  addWords() {
    let words = Array.from(arguments);

    this.list.push(...words);

    words
      .map(word => word.toLowerCase())
      .forEach((word) => {
        if (this.exclude.includes(word)) {
          this.exclude.splice(this.exclude.indexOf(word), 1);
        }
      });
  }

  /**
   * Add words to whitelist filter
   * @param {...string} word - Word(s) to add to whitelist.
   */
  removeWords() {
    this.exclude.push(...Array.from(arguments).map(word => word.toLowerCase()));
  }
}

module.exports = Filter;

/***/ }),

/***/ "./node_modules/badwords-list/lib/array.js":
/*!*************************************************!*\
  !*** ./node_modules/badwords-list/lib/array.js ***!
  \*************************************************/
/***/ (function(module) {

module.exports = ["4r5e", "5h1t", "5hit", "a55", "anal", "anus", "ar5e", "arrse", "arse", "ass", "ass-fucker", "asses", "assfucker", "assfukka", "asshole", "assholes", "asswhole", "a_s_s", "b!tch", "b00bs", "b17ch", "b1tch", "ballbag", "balls", "ballsack", "bastard", "beastial", "beastiality", "bellend", "bestial", "bestiality", "bi+ch", "biatch", "bitch", "bitcher", "bitchers", "bitches", "bitchin", "bitching", "bloody", "blow job", "blowjob", "blowjobs", "boiolas", "bollock", "bollok", "boner", "boob", "boobs", "booobs", "boooobs", "booooobs", "booooooobs", "breasts", "buceta", "bugger", "bum", "bunny fucker", "butt", "butthole", "buttmuch", "buttplug", "c0ck", "c0cksucker", "carpet muncher", "cawk", "chink", "cipa", "cl1t", "clit", "clitoris", "clits", "cnut", "cock", "cock-sucker", "cockface", "cockhead", "cockmunch", "cockmuncher", "cocks", "cocksuck", "cocksucked", "cocksucker", "cocksucking", "cocksucks", "cocksuka", "cocksukka", "cok", "cokmuncher", "coksucka", "coon", "cox", "crap", "cum", "cummer", "cumming", "cums", "cumshot", "cunilingus", "cunillingus", "cunnilingus", "cunt", "cuntlick", "cuntlicker", "cuntlicking", "cunts", "cyalis", "cyberfuc", "cyberfuck", "cyberfucked", "cyberfucker", "cyberfuckers", "cyberfucking", "d1ck", "damn", "dick", "dickhead", "dildo", "dildos", "dink", "dinks", "dirsa", "dlck", "dog-fucker", "doggin", "dogging", "donkeyribber", "doosh", "duche", "dyke", "ejaculate", "ejaculated", "ejaculates", "ejaculating", "ejaculatings", "ejaculation", "ejakulate", "f u c k", "f u c k e r", "f4nny", "fag", "fagging", "faggitt", "faggot", "faggs", "fagot", "fagots", "fags", "fanny", "fannyflaps", "fannyfucker", "fanyy", "fatass", "fcuk", "fcuker", "fcuking", "feck", "fecker", "felching", "fellate", "fellatio", "fingerfuck", "fingerfucked", "fingerfucker", "fingerfuckers", "fingerfucking", "fingerfucks", "fistfuck", "fistfucked", "fistfucker", "fistfuckers", "fistfucking", "fistfuckings", "fistfucks", "flange", "fook", "fooker", "fuck", "fucka", "fucked", "fucker", "fuckers", "fuckhead", "fuckheads", "fuckin", "fucking", "fuckings", "fuckingshitmotherfucker", "fuckme", "fucks", "fuckwhit", "fuckwit", "fudge packer", "fudgepacker", "fuk", "fuker", "fukker", "fukkin", "fuks", "fukwhit", "fukwit", "fux", "fux0r", "f_u_c_k", "gangbang", "gangbanged", "gangbangs", "gaylord", "gaysex", "goatse", "God", "god-dam", "god-damned", "goddamn", "goddamned", "hardcoresex", "hell", "heshe", "hoar", "hoare", "hoer", "homo", "hore", "horniest", "horny", "hotsex", "jack-off", "jackoff", "jap", "jerk-off", "jism", "jiz", "jizm", "jizz", "kawk", "knob", "knobead", "knobed", "knobend", "knobhead", "knobjocky", "knobjokey", "kock", "kondum", "kondums", "kum", "kummer", "kumming", "kums", "kunilingus", "l3i+ch", "l3itch", "labia", "lust", "lusting", "m0f0", "m0fo", "m45terbate", "ma5terb8", "ma5terbate", "masochist", "master-bate", "masterb8", "masterbat*", "masterbat3", "masterbate", "masterbation", "masterbations", "masturbate", "mo-fo", "mof0", "mofo", "mothafuck", "mothafucka", "mothafuckas", "mothafuckaz", "mothafucked", "mothafucker", "mothafuckers", "mothafuckin", "mothafucking", "mothafuckings", "mothafucks", "mother fucker", "motherfuck", "motherfucked", "motherfucker", "motherfuckers", "motherfuckin", "motherfucking", "motherfuckings", "motherfuckka", "motherfucks", "muff", "mutha", "muthafecker", "muthafuckker", "muther", "mutherfucker", "n1gga", "n1gger", "nazi", "nigg3r", "nigg4h", "nigga", "niggah", "niggas", "niggaz", "nigger", "niggers", "nob", "nob jokey", "nobhead", "nobjocky", "nobjokey", "numbnuts", "nutsack", "orgasim", "orgasims", "orgasm", "orgasms", "p0rn", "pawn", "pecker", "penis", "penisfucker", "phonesex", "phuck", "phuk", "phuked", "phuking", "phukked", "phukking", "phuks", "phuq", "pigfucker", "pimpis", "piss", "pissed", "pisser", "pissers", "pisses", "pissflaps", "pissin", "pissing", "pissoff", "poop", "porn", "porno", "pornography", "pornos", "prick", "pricks", "pron", "pube", "pusse", "pussi", "pussies", "pussy", "pussys", "rectum", "retard", "rimjaw", "rimming", "s hit", "s.o.b.", "sadist", "schlong", "screwing", "scroat", "scrote", "scrotum", "semen", "sex", "sh!+", "sh!t", "sh1t", "shag", "shagger", "shaggin", "shagging", "shemale", "shi+", "shit", "shitdick", "shite", "shited", "shitey", "shitfuck", "shitfull", "shithead", "shiting", "shitings", "shits", "shitted", "shitter", "shitters", "shitting", "shittings", "shitty", "skank", "slut", "sluts", "smegma", "smut", "snatch", "son-of-a-bitch", "spac", "spunk", "s_h_i_t", "t1tt1e5", "t1tties", "teets", "teez", "testical", "testicle", "tit", "titfuck", "tits", "titt", "tittie5", "tittiefucker", "titties", "tittyfuck", "tittywank", "titwank", "tosser", "turd", "tw4t", "twat", "twathead", "twatty", "twunt", "twunter", "v14gra", "v1gra", "vagina", "viagra", "vulva", "w00se", "wang", "wank", "wanker", "wanky", "whoar", "whore", "willies", "willy", "xrated", "xxx"];

/***/ }),

/***/ "./node_modules/badwords-list/lib/index.js":
/*!*************************************************!*\
  !*** ./node_modules/badwords-list/lib/index.js ***!
  \*************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = {
  object: __webpack_require__(/*! ./object */ "./node_modules/badwords-list/lib/object.js"),
  array: __webpack_require__(/*! ./array */ "./node_modules/badwords-list/lib/array.js"),
  regex: __webpack_require__(/*! ./regexp */ "./node_modules/badwords-list/lib/regexp.js")
};

/***/ }),

/***/ "./node_modules/badwords-list/lib/object.js":
/*!**************************************************!*\
  !*** ./node_modules/badwords-list/lib/object.js ***!
  \**************************************************/
/***/ (function(module) {

module.exports = {"4r5e": 1, "5h1t": 1, "5hit": 1, "a55": 1, "anal": 1, "anus": 1, "ar5e": 1, "arrse": 1, "arse": 1, "ass": 1, "ass-fucker": 1, "asses": 1, "assfucker": 1, "assfukka": 1, "asshole": 1, "assholes": 1, "asswhole": 1, "a_s_s": 1, "b!tch": 1, "b00bs": 1, "b17ch": 1, "b1tch": 1, "ballbag": 1, "balls": 1, "ballsack": 1, "bastard": 1, "beastial": 1, "beastiality": 1, "bellend": 1, "bestial": 1, "bestiality": 1, "bi+ch": 1, "biatch": 1, "bitch": 1, "bitcher": 1, "bitchers": 1, "bitches": 1, "bitchin": 1, "bitching": 1, "bloody": 1, "blow job": 1, "blowjob": 1, "blowjobs": 1, "boiolas": 1, "bollock": 1, "bollok": 1, "boner": 1, "boob": 1, "boobs": 1, "booobs": 1, "boooobs": 1, "booooobs": 1, "booooooobs": 1, "breasts": 1, "buceta": 1, "bugger": 1, "bum": 1, "bunny fucker": 1, "butt": 1, "butthole": 1, "buttmuch": 1, "buttplug": 1, "c0ck": 1, "c0cksucker": 1, "carpet muncher": 1, "cawk": 1, "chink": 1, "cipa": 1, "cl1t": 1, "clit": 1, "clitoris": 1, "clits": 1, "cnut": 1, "cock": 1, "cock-sucker": 1, "cockface": 1, "cockhead": 1, "cockmunch": 1, "cockmuncher": 1, "cocks": 1, "cocksuck": 1, "cocksucked": 1, "cocksucker": 1, "cocksucking": 1, "cocksucks": 1, "cocksuka": 1, "cocksukka": 1, "cok": 1, "cokmuncher": 1, "coksucka": 1, "coon": 1, "cox": 1, "crap": 1, "cum": 1, "cummer": 1, "cumming": 1, "cums": 1, "cumshot": 1, "cunilingus": 1, "cunillingus": 1, "cunnilingus": 1, "cunt": 1, "cuntlick": 1, "cuntlicker": 1, "cuntlicking": 1, "cunts": 1, "cyalis": 1, "cyberfuc": 1, "cyberfuck": 1, "cyberfucked": 1, "cyberfucker": 1, "cyberfuckers": 1, "cyberfucking": 1, "d1ck": 1, "damn": 1, "dick": 1, "dickhead": 1, "dildo": 1, "dildos": 1, "dink": 1, "dinks": 1, "dirsa": 1, "dlck": 1, "dog-fucker": 1, "doggin": 1, "dogging": 1, "donkeyribber": 1, "doosh": 1, "duche": 1, "dyke": 1, "ejaculate": 1, "ejaculated": 1, "ejaculates": 1, "ejaculating": 1, "ejaculatings": 1, "ejaculation": 1, "ejakulate": 1, "f u c k": 1, "f u c k e r": 1, "f4nny": 1, "fag": 1, "fagging": 1, "faggitt": 1, "faggot": 1, "faggs": 1, "fagot": 1, "fagots": 1, "fags": 1, "fanny": 1, "fannyflaps": 1, "fannyfucker": 1, "fanyy": 1, "fatass": 1, "fcuk": 1, "fcuker": 1, "fcuking": 1, "feck": 1, "fecker": 1, "felching": 1, "fellate": 1, "fellatio": 1, "fingerfuck": 1, "fingerfucked": 1, "fingerfucker": 1, "fingerfuckers": 1, "fingerfucking": 1, "fingerfucks": 1, "fistfuck": 1, "fistfucked": 1, "fistfucker": 1, "fistfuckers": 1, "fistfucking": 1, "fistfuckings": 1, "fistfucks": 1, "flange": 1, "fook": 1, "fooker": 1, "fuck": 1, "fucka": 1, "fucked": 1, "fucker": 1, "fuckers": 1, "fuckhead": 1, "fuckheads": 1, "fuckin": 1, "fucking": 1, "fuckings": 1, "fuckingshitmotherfucker": 1, "fuckme": 1, "fucks": 1, "fuckwhit": 1, "fuckwit": 1, "fudge packer": 1, "fudgepacker": 1, "fuk": 1, "fuker": 1, "fukker": 1, "fukkin": 1, "fuks": 1, "fukwhit": 1, "fukwit": 1, "fux": 1, "fux0r": 1, "f_u_c_k": 1, "gangbang": 1, "gangbanged": 1, "gangbangs": 1, "gaylord": 1, "gaysex": 1, "goatse": 1, "God": 1, "god-dam": 1, "god-damned": 1, "goddamn": 1, "goddamned": 1, "hardcoresex": 1, "hell": 1, "heshe": 1, "hoar": 1, "hoare": 1, "hoer": 1, "homo": 1, "hore": 1, "horniest": 1, "horny": 1, "hotsex": 1, "jack-off": 1, "jackoff": 1, "jap": 1, "jerk-off": 1, "jism": 1, "jiz": 1, "jizm": 1, "jizz": 1, "kawk": 1, "knob": 1, "knobead": 1, "knobed": 1, "knobend": 1, "knobhead": 1, "knobjocky": 1, "knobjokey": 1, "kock": 1, "kondum": 1, "kondums": 1, "kum": 1, "kummer": 1, "kumming": 1, "kums": 1, "kunilingus": 1, "l3i+ch": 1, "l3itch": 1, "labia": 1, "lust": 1, "lusting": 1, "m0f0": 1, "m0fo": 1, "m45terbate": 1, "ma5terb8": 1, "ma5terbate": 1, "masochist": 1, "master-bate": 1, "masterb8": 1, "masterbat*": 1, "masterbat3": 1, "masterbate": 1, "masterbation": 1, "masterbations": 1, "masturbate": 1, "mo-fo": 1, "mof0": 1, "mofo": 1, "mothafuck": 1, "mothafucka": 1, "mothafuckas": 1, "mothafuckaz": 1, "mothafucked": 1, "mothafucker": 1, "mothafuckers": 1, "mothafuckin": 1, "mothafucking": 1, "mothafuckings": 1, "mothafucks": 1, "mother fucker": 1, "motherfuck": 1, "motherfucked": 1, "motherfucker": 1, "motherfuckers": 1, "motherfuckin": 1, "motherfucking": 1, "motherfuckings": 1, "motherfuckka": 1, "motherfucks": 1, "muff": 1, "mutha": 1, "muthafecker": 1, "muthafuckker": 1, "muther": 1, "mutherfucker": 1, "n1gga": 1, "n1gger": 1, "nazi": 1, "nigg3r": 1, "nigg4h": 1, "nigga": 1, "niggah": 1, "niggas": 1, "niggaz": 1, "nigger": 1, "niggers": 1, "nob": 1, "nob jokey": 1, "nobhead": 1, "nobjocky": 1, "nobjokey": 1, "numbnuts": 1, "nutsack": 1, "orgasim": 1, "orgasims": 1, "orgasm": 1, "orgasms": 1, "p0rn": 1, "pawn": 1, "pecker": 1, "penis": 1, "penisfucker": 1, "phonesex": 1, "phuck": 1, "phuk": 1, "phuked": 1, "phuking": 1, "phukked": 1, "phukking": 1, "phuks": 1, "phuq": 1, "pigfucker": 1, "pimpis": 1, "piss": 1, "pissed": 1, "pisser": 1, "pissers": 1, "pisses": 1, "pissflaps": 1, "pissin": 1, "pissing": 1, "pissoff": 1, "poop": 1, "porn": 1, "porno": 1, "pornography": 1, "pornos": 1, "prick": 1, "pricks": 1, "pron": 1, "pube": 1, "pusse": 1, "pussi": 1, "pussies": 1, "pussy": 1, "pussys": 1, "rectum": 1, "retard": 1, "rimjaw": 1, "rimming": 1, "s hit": 1, "s.o.b.": 1, "sadist": 1, "schlong": 1, "screwing": 1, "scroat": 1, "scrote": 1, "scrotum": 1, "semen": 1, "sex": 1, "sh!+": 1, "sh!t": 1, "sh1t": 1, "shag": 1, "shagger": 1, "shaggin": 1, "shagging": 1, "shemale": 1, "shi+": 1, "shit": 1, "shitdick": 1, "shite": 1, "shited": 1, "shitey": 1, "shitfuck": 1, "shitfull": 1, "shithead": 1, "shiting": 1, "shitings": 1, "shits": 1, "shitted": 1, "shitter": 1, "shitters": 1, "shitting": 1, "shittings": 1, "shitty": 1, "skank": 1, "slut": 1, "sluts": 1, "smegma": 1, "smut": 1, "snatch": 1, "son-of-a-bitch": 1, "spac": 1, "spunk": 1, "s_h_i_t": 1, "t1tt1e5": 1, "t1tties": 1, "teets": 1, "teez": 1, "testical": 1, "testicle": 1, "tit": 1, "titfuck": 1, "tits": 1, "titt": 1, "tittie5": 1, "tittiefucker": 1, "titties": 1, "tittyfuck": 1, "tittywank": 1, "titwank": 1, "tosser": 1, "turd": 1, "tw4t": 1, "twat": 1, "twathead": 1, "twatty": 1, "twunt": 1, "twunter": 1, "v14gra": 1, "v1gra": 1, "vagina": 1, "viagra": 1, "vulva": 1, "w00se": 1, "wang": 1, "wank": 1, "wanker": 1, "wanky": 1, "whoar": 1, "whore": 1, "willies": 1, "willy": 1, "xrated": 1, "xxx": 1};

/***/ }),

/***/ "./node_modules/badwords-list/lib/regexp.js":
/*!**************************************************!*\
  !*** ./node_modules/badwords-list/lib/regexp.js ***!
  \**************************************************/
/***/ (function(module) {

module.exports = /\b(4r5e|5h1t|5hit|a55|anal|anus|ar5e|arrse|arse|ass|ass-fucker|asses|assfucker|assfukka|asshole|assholes|asswhole|a_s_s|b!tch|b00bs|b17ch|b1tch|ballbag|balls|ballsack|bastard|beastial|beastiality|bellend|bestial|bestiality|bi\+ch|biatch|bitch|bitcher|bitchers|bitches|bitchin|bitching|bloody|blow job|blowjob|blowjobs|boiolas|bollock|bollok|boner|boob|boobs|booobs|boooobs|booooobs|booooooobs|breasts|buceta|bugger|bum|bunny fucker|butt|butthole|buttmuch|buttplug|c0ck|c0cksucker|carpet muncher|cawk|chink|cipa|cl1t|clit|clitoris|clits|cnut|cock|cock-sucker|cockface|cockhead|cockmunch|cockmuncher|cocks|cocksuck|cocksucked|cocksucker|cocksucking|cocksucks|cocksuka|cocksukka|cok|cokmuncher|coksucka|coon|cox|crap|cum|cummer|cumming|cums|cumshot|cunilingus|cunillingus|cunnilingus|cunt|cuntlick|cuntlicker|cuntlicking|cunts|cyalis|cyberfuc|cyberfuck|cyberfucked|cyberfucker|cyberfuckers|cyberfucking|d1ck|damn|dick|dickhead|dildo|dildos|dink|dinks|dirsa|dlck|dog-fucker|doggin|dogging|donkeyribber|doosh|duche|dyke|ejaculate|ejaculated|ejaculates|ejaculating|ejaculatings|ejaculation|ejakulate|f u c k|f u c k e r|f4nny|fag|fagging|faggitt|faggot|faggs|fagot|fagots|fags|fanny|fannyflaps|fannyfucker|fanyy|fatass|fcuk|fcuker|fcuking|feck|fecker|felching|fellate|fellatio|fingerfuck|fingerfucked|fingerfucker|fingerfuckers|fingerfucking|fingerfucks|fistfuck|fistfucked|fistfucker|fistfuckers|fistfucking|fistfuckings|fistfucks|flange|fook|fooker|fuck|fucka|fucked|fucker|fuckers|fuckhead|fuckheads|fuckin|fucking|fuckings|fuckingshitmotherfucker|fuckme|fucks|fuckwhit|fuckwit|fudge packer|fudgepacker|fuk|fuker|fukker|fukkin|fuks|fukwhit|fukwit|fux|fux0r|f_u_c_k|gangbang|gangbanged|gangbangs|gaylord|gaysex|goatse|God|god-dam|god-damned|goddamn|goddamned|hardcoresex|hell|heshe|hoar|hoare|hoer|homo|hore|horniest|horny|hotsex|jack-off|jackoff|jap|jerk-off|jism|jiz|jizm|jizz|kawk|knob|knobead|knobed|knobend|knobhead|knobjocky|knobjokey|kock|kondum|kondums|kum|kummer|kumming|kums|kunilingus|l3i\+ch|l3itch|labia|lust|lusting|m0f0|m0fo|m45terbate|ma5terb8|ma5terbate|masochist|master-bate|masterb8|masterbat*|masterbat3|masterbate|masterbation|masterbations|masturbate|mo-fo|mof0|mofo|mothafuck|mothafucka|mothafuckas|mothafuckaz|mothafucked|mothafucker|mothafuckers|mothafuckin|mothafucking|mothafuckings|mothafucks|mother fucker|motherfuck|motherfucked|motherfucker|motherfuckers|motherfuckin|motherfucking|motherfuckings|motherfuckka|motherfucks|muff|mutha|muthafecker|muthafuckker|muther|mutherfucker|n1gga|n1gger|nazi|nigg3r|nigg4h|nigga|niggah|niggas|niggaz|nigger|niggers|nob|nob jokey|nobhead|nobjocky|nobjokey|numbnuts|nutsack|orgasim|orgasims|orgasm|orgasms|p0rn|pawn|pecker|penis|penisfucker|phonesex|phuck|phuk|phuked|phuking|phukked|phukking|phuks|phuq|pigfucker|pimpis|piss|pissed|pisser|pissers|pisses|pissflaps|pissin|pissing|pissoff|poop|porn|porno|pornography|pornos|prick|pricks|pron|pube|pusse|pussi|pussies|pussy|pussys|rectum|retard|rimjaw|rimming|s hit|s.o.b.|sadist|schlong|screwing|scroat|scrote|scrotum|semen|sex|sh!\+|sh!t|sh1t|shag|shagger|shaggin|shagging|shemale|shi\+|shit|shitdick|shite|shited|shitey|shitfuck|shitfull|shithead|shiting|shitings|shits|shitted|shitter|shitters|shitting|shittings|shitty|skank|slut|sluts|smegma|smut|snatch|son-of-a-bitch|spac|spunk|s_h_i_t|t1tt1e5|t1tties|teets|teez|testical|testicle|tit|titfuck|tits|titt|tittie5|tittiefucker|titties|tittyfuck|tittywank|titwank|tosser|turd|tw4t|twat|twathead|twatty|twunt|twunter|v14gra|v1gra|vagina|viagra|vulva|w00se|wang|wank|wanker|wanky|whoar|whore|willies|willy|xrated|xxx)\b/gi;

/***/ }),

/***/ "./Modules/Chat/Resources/assets/js/create_group_livewire.js":
/*!*******************************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/create_group_livewire.js ***!
  \*******************************************************************/
/***/ (function() {

$(document).on('click', '.remove-group-member', function () {
  var removeItem = $(this).data('id');
  addedMembers = removeItemFromArray(addedMembers, removeItem);
  $('#selectedGroupMembers').val(JSON.stringify(addedMembers));
  if ($(this).parents('#modalAddGroupMembers').length) {
    $('#selectedGroupMembersForEdit').val(JSON.stringify(addedMembers));
  }
  checkAddedMemberListLengthWhileRemoving($(this), 1);
  $(this).parents('.list-group-item').remove();
});
$('#searchMember').keyup(debounce(function () {
  var searchVal = $(this).val();
  window.livewire.emit('searchMembers', searchVal, addedMembers);
}, 500));
document.getElementById("searchMember").addEventListener("search", function (event) {
  var searchVal = $(this).val();
  window.livewire.emit('searchMembers', searchVal, addedMembers);
});
document.getElementById("searchEditGroupMember").addEventListener("search", function (event) {
  var searchVal = $(this).val();
  window.livewire.emit('searchEditGroupMembers', searchVal, addedMembers);
});
$('#searchEditGroupMember').keyup(debounce(function () {
  var searchVal = $(this).val();
  window.livewire.emit('searchEditGroupMembers', searchVal, addedMembers);
}, 500));
$(document).on('click', '.add-group-member', function () {
  var memberUi = $(this).parents('.list-group-item');
  var memberId = $(this).data('id');
  addedMembers.push(memberId);
  $('#selectedGroupMembers').val(JSON.stringify(addedMembers));
  if ($(this).parents('#modalAddGroupMembers').length) {
    $('#selectedGroupMembersForEdit').val(JSON.stringify(addedMembers));
  }
  addMembers(memberUi);
  checkAddedMemberListLength($(this));
  checkMemberListLength($(this));
  $(this).parents('.list-group-item').remove();
});
function checkMemberListLength(ele) {
  var remainingMembers = ele.parents('.group-members-list').find('li').length;
  if (remainingMembers > 1) {
    ele.parents('.group-members-list').find('.no-member-found').removeClass('d-none').addClass('d-none');
  } else {
    ele.parents('.group-members-list').find('.no-member-found').removeClass('d-none');
  }
}
function checkAddedMemberListLength(ele) {
  var isRemainingMemberLengthOne = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var membersEle = ele.parents('.new-members').siblings('.added-members');
  var remainingMembers = membersEle.find('li').length;
  var remainingMembersLengthCondition = isRemainingMemberLengthOne ? 1 : 0;
  if (remainingMembers > remainingMembersLengthCondition) {
    membersEle.find('.no-member-added').removeClass('d-none').addClass('d-none');
  } else {
    membersEle.find('.no-member-added').removeClass('d-none');
  }
}
function checkAddedMemberListLengthWhileRemoving(ele) {
  var isRemainingMemberLengthOne = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var membersEle = ele.parents('.added-members');
  var remainingMembers = membersEle.find('li').length;
  var remainingMembersLengthCondition = isRemainingMemberLengthOne ? 1 : 0;
  if (remainingMembers > remainingMembersLengthCondition) {
    membersEle.find('.no-member-added').removeClass('d-none').addClass('d-none');
  } else {
    membersEle.find('.no-member-added').removeClass('d-none');
  }
}
function addMembers(memberUi) {
  var membersEle = memberUi.parents('.new-members').siblings('.added-members').find('.added-group-members-list');
  memberUi.find('.add-group-member').text('Remove').addClass('btn-danger').addClass('remove-group-member').removeClass('add-group-member').removeClass('btn-success');
  memberUi = memberUi.html();
  var memberUiList = '<li class="mb-0 list-group-item group-members-list-chat-select__list-item align-items-center d-flex justify-content-between">' + memberUi + '</li>';
  membersEle.prepend(memberUiList);
}
function removeItemFromArray(arrayEle, removeItem) {
  arrayEle = jQuery.grep(arrayEle, function (value) {
    return value != removeItem;
  });
  return arrayEle;
}
function debounce(func, wait, immediate) {
  var timeout;
  return function () {
    var context = this,
      args = arguments;
    var later = function later() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}
;

/***/ }),

/***/ "./node_modules/bad-words/lib/lang.json":
/*!**********************************************!*\
  !*** ./node_modules/bad-words/lib/lang.json ***!
  \**********************************************/
/***/ (function(module) {

"use strict";
module.exports = JSON.parse('{"words":["ahole","anus","ash0le","ash0les","asholes","ass","Ass Monkey","Assface","assh0le","assh0lez","asshole","assholes","assholz","asswipe","azzhole","bassterds","bastard","bastards","bastardz","basterds","basterdz","Biatch","bitch","bitches","Blow Job","boffing","butthole","buttwipe","c0ck","c0cks","c0k","Carpet Muncher","cawk","cawks","Clit","cnts","cntz","cock","cockhead","cock-head","cocks","CockSucker","cock-sucker","crap","cum","cunt","cunts","cuntz","dick","dild0","dild0s","dildo","dildos","dilld0","dilld0s","dominatricks","dominatrics","dominatrix","dyke","enema","f u c k","f u c k e r","fag","fag1t","faget","fagg1t","faggit","faggot","fagg0t","fagit","fags","fagz","faig","faigs","fart","flipping the bird","fuck","fucker","fuckin","fucking","fucks","Fudge Packer","fuk","Fukah","Fuken","fuker","Fukin","Fukk","Fukkah","Fukken","Fukker","Fukkin","g00k","God-damned","h00r","h0ar","h0re","hells","hoar","hoor","hoore","jackoff","jap","japs","jerk-off","jisim","jiss","jizm","jizz","knob","knobs","knobz","kunt","kunts","kuntz","Lezzian","Lipshits","Lipshitz","masochist","masokist","massterbait","masstrbait","masstrbate","masterbaiter","masterbate","masterbates","Motha Fucker","Motha Fuker","Motha Fukkah","Motha Fukker","Mother Fucker","Mother Fukah","Mother Fuker","Mother Fukkah","Mother Fukker","mother-fucker","Mutha Fucker","Mutha Fukah","Mutha Fuker","Mutha Fukkah","Mutha Fukker","n1gr","nastt","nigger;","nigur;","niiger;","niigr;","orafis","orgasim;","orgasm","orgasum","oriface","orifice","orifiss","packi","packie","packy","paki","pakie","paky","pecker","peeenus","peeenusss","peenus","peinus","pen1s","penas","penis","penis-breath","penus","penuus","Phuc","Phuck","Phuk","Phuker","Phukker","polac","polack","polak","Poonani","pr1c","pr1ck","pr1k","pusse","pussee","pussy","puuke","puuker","qweir","recktum","rectum","retard","sadist","scank","schlong","screwing","semen","sex","sexy","Sh!t","sh1t","sh1ter","sh1ts","sh1tter","sh1tz","shit","shits","shitter","Shitty","Shity","shitz","Shyt","Shyte","Shytty","Shyty","skanck","skank","skankee","skankey","skanks","Skanky","slag","slut","sluts","Slutty","slutz","son-of-a-bitch","tit","turd","va1jina","vag1na","vagiina","vagina","vaj1na","vajina","vullva","vulva","w0p","wh00r","wh0re","whore","xrated","xxx","b!+ch","bitch","blowjob","clit","arschloch","fuck","shit","ass","asshole","b!tch","b17ch","b1tch","bastard","bi+ch","boiolas","buceta","c0ck","cawk","chink","cipa","clits","cock","cum","cunt","dildo","dirsa","ejakulate","fatass","fcuk","fuk","fux0r","hoer","hore","jism","kawk","l3itch","l3i+ch","masturbate","masterbat*","masterbat3","motherfucker","s.o.b.","mofo","nazi","nigga","nigger","nutsack","phuck","pimpis","pusse","pussy","scrotum","sh!t","shemale","shi+","sh!+","slut","smut","teets","tits","boobs","b00bs","teez","testical","testicle","titt","w00se","jackoff","wank","whoar","whore","*damn","*dyke","*fuck*","*shit*","@$$","amcik","andskota","arse*","assrammer","ayir","bi7ch","bitch*","bollock*","breasts","butt-pirate","cabron","cazzo","chraa","chuj","Cock*","cunt*","d4mn","daygo","dego","dick*","dike*","dupa","dziwka","ejackulate","Ekrem*","Ekto","enculer","faen","fag*","fanculo","fanny","feces","feg","Felcher","ficken","fitt*","Flikker","foreskin","Fotze","Fu(*","fuk*","futkretzn","gook","guiena","h0r","h4x0r","hell","helvete","hoer*","honkey","Huevon","hui","injun","jizz","kanker*","kike","klootzak","kraut","knulle","kuk","kuksuger","Kurac","kurwa","kusi*","kyrpa*","lesbo","mamhoon","masturbat*","merd*","mibun","monkleigh","mouliewop","muie","mulkku","muschi","nazis","nepesaurio","nigger*","orospu","paska*","perse","picka","pierdol*","pillu*","pimmel","piss*","pizda","poontsee","poop","porn","p0rn","pr0n","preteen","pula","pule","puta","puto","qahbeh","queef*","rautenberg","schaffer","scheiss*","schlampe","schmuck","screw","sh!t*","sharmuta","sharmute","shipal","shiz","skribz","skurwysyn","sphencter","spic","spierdalaj","splooge","suka","b00b*","testicle*","titt*","twat","vittu","wank*","wetback*","wichser","wop*","yed","zabourah"]}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
/*!**************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/chat.js ***!
  \**************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _create_group_livewire__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create_group_livewire */ "./Modules/Chat/Resources/assets/js/create_group_livewire.js");
/* harmony import */ var _create_group_livewire__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_create_group_livewire__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

var chatSendArea = $.templates('#tmplChatSendArea').render();
var newMessageIndicator = $.templates('#tmplNewMsgIndicator').render();
var blockedMessageText = $.templates('#tmplBlockMsgText').render();
var blockedByMessageText = $.templates('#tmplBlockByMsgText').render();
var hdnTextMessage = $.templates('#tmplHiddenTxtMsg').render();
var closedGroupIcon = $.templates('#tmplCloseGroupIcon').render();
var privateGroupIcon = $.templates('#tmplPrivateGroupIcon').render();
var totalUnreadConversations = 0;
var unreadMessageIds = [];
var readMessageFunctionInterval = false;
var readedMessageIds = [];
var newConversationStartedUserIds = [];
var blockedUsersList = [];
var myContactIds = [];
$.each(blockedUsersListObj, function (index, val) {
  blockedUsersList.push(val);
});
$.each(myContactIdsObj, function (index, val) {
  myContactIds.push(val);
});

// we are loading 8000 messages at a time, so when real time messages are occurs,
// scroll is moved to bottom and then after?msg_id=123 api is calling which should be not called
// so this variable prevent that behaviour, and not call that api when realtime messages are coming
var callAfterAPI = true;
var callBeforeAPI = false;
var noConversationYet = false;
var noArchiveConversationYet = false;
var noConversationEle = $('.no-conversation');
var noArchiveConversationEle = $('.no-archive-conversation');
var noConversationYetEle = $('.no-conversation-yet');
var noArchiveConversationYetEle = $('.no-archive-conversation-yet');
noConversationEle.hide();
noArchiveConversationEle.hide();
noConversationYetEle.hide();
noArchiveConversationYetEle.hide();
noConversationEle.removeClass('d-none');
$(document).ready(function () {
  var mediaTypeImage = 1;
  var mediaTypePdf = 2;
  var mediaTypeDocx = 3;
  var mediaTypeVoice = 4;
  var mediaTypeVideo = 5;
  var youtubeUrl = 6;
  var mediaTypeTxt = 7;
  var mediaTypeXls = 8;
  var mediaTypeZip = 10;
  var selectedContactId = '';
  var selectedContactImg = '';
  var timer = null;
  var myRoleInGroup,
    currentSelectedGroupId = null;
  var startTyping = true;
  var lastMessageIdForScroll = '';
  var limit = 20;
  var shouldCallApiTop = true;
  var shouldCallApiBottom = true;
  var placeCaret = true;
  var scrollAtLastMsg = false;
  var isAllMessagesRead = false;
  var appendGroupMessagesAtLast = true;
  var previousDate = [];
  var isDraftMsg = 0;
  var groupMembers = [];
  var groupMembersInfo = [];
  var conversationMessages = [];
  var currentConversationGroupId = '';
  var isAccepted = false;
  var chatPeopleBodyEle = $('#chatPeopleBody');
  var archivePeopleBodyEle = $('#archivePeopleBody');
  var noMsgesYet = $('.chat__not-selected');
  var membersCountArr = [];
  $('#myTab a').on('hidden.bs.tab', function () {
    clearSearchAndFilterOfMyContactsTab();
    clearSearchAndFilterOfNewContactsTab();
    clearSearchOfGroupsTab();
    clearSearchOfBlockedContactsTab();
  });
  $('input[type="radio"]').iCheck({
    checkboxClass: 'icheckbox_square',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' // optional
  });

  //add a event listener to the window that calls preventDefault() on all dragover and drop events.
  window.addEventListener('dragover', function (e) {
    e = e || event;
    e.preventDefault();
  }, false);
  window.addEventListener('drop', function (e) {
    e = e || event;
    e.preventDefault();
  }, false);
  window.isInBlockedList = function (userId) {
    if ($.inArray(userId, blockedUsersList) != -1) {
      return false;
    }
    return true;
  };

  // Get Groups List
  $.ajax({
    type: 'GET',
    url: route('groups.index'),
    success: function success(result) {
      if (result.success) {
        $.each(result.data, function (i, group) {
          listenForGroupUpdates(group.id);
        });
      }
    },
    error: function error(_error) {
      console.log(_error);
    }
  });
  $.ajax({
    type: 'GET',
    url: route('conversations-list', {
      type: conversationType
    }),
    success: function success(data) {
      if (data.success) {
        $('#infyLoader').hide();
        var latestConversations = data.data.conversations;
        if (latestConversations.length === 0) {
          noConversationYetEle.show();
          noConversationYet = true;
        }
        chatPeopleBodyEle.append(latestConversations.map(prepareContacts).join(''));
        searchUsers();
        loadTooltip();
        if (data.data.conversations.length > 9) {
          var $loadMoreBtn = $('#loadMoreConversationBtn').clone();
          $('#chatPeopleBody').append($loadMoreBtn);
          $loadMoreBtn.addClass('active');
          $loadMoreBtn.show();
        }
      }
    },
    error: function error(_error2) {
      console.log(_error2);
    }
  });

  //GET Archive conversations list
  $.ajax({
    type: 'GET',
    url: route('archive-conversations', {
      type: conversationType
    }),
    success: function success(data) {
      if (data.success) {
        var archiveConversations = data.data.conversations;
        if (archiveConversations.length === 0) {
          noArchiveConversationYetEle.show();
          noArchiveConversationYet = true;
        }
        archivePeopleBodyEle.append(archiveConversations.map(prepareContacts).join(''));
        archivePeopleBodyEle.find('.chat__person-box-archive').each(function () {
          $(this).text('Unarchive Chat');
        });
        searchUsers();
        if (data.data.conversations.length > 9) {
          var $loadMoreBtn = $('#loadMoreArchiverConversationBtn').clone();
          $('#archivePeopleBody').append($loadMoreBtn);
          $loadMoreBtn.addClass('active');
          $loadMoreBtn.show();
        }
      }
    },
    error: function error(_error3) {
      console.log(_error3);
    }
  });

  // bind click for recent chat user select
  var latestSelectedUser;
  var textMessageEle = $('#textMessage');
  $(document).on('click', '.chat__person-box', function (e) {
    if (isAccepted == false && $(e.currentTarget).data('id') == selectedContactId) {
      return false;
    }
    callBeforeAPI = false;
    $('.chat-conversation').html('');
    noConversationEle.hide();
    scrollAtLastMsg = false;
    callAfterAPI = true;
    previousDate = [];
    isDraftMsg = 0;
    $(document).find('.chat__person-box').removeClass('chat__person-box--active');
    selectedContactId = $(e.currentTarget).data('id');
    $('.chat__people-body').find('#user-' + selectedContactId).addClass('chat__person-box--active');
    var isGroup = $(e.currentTarget).data('is_group');
    var lastDraftMsg = getLocalStorageItem('user_' + selectedContactId);
    var urlDetail = userURL + selectedContactId + '/conversation';
    $.ajax({
      type: 'GET',
      url: urlDetail,
      data: {
        'is_group': isGroup
      },
      success: function success(data) {
        shouldCallApiTop = true;
        shouldCallApiBottom = true;
        callBeforeAPI = true;
        var lastMsg = data.data.conversations[data.data.conversations.length - 1];
        lastMessageIdForScroll = data.data.conversations.length !== 0 ? lastMsg.id : 0;
        latestSelectedUser = $('.chat__person-box--active').data('id');
        //put this (latestSelectedUser == data.user.id) condition bcz if responce come little late and user has already switch to another user than it shows data blink
        var groupOrUserObj = null;
        if (data.data.user === null) {
          groupOrUserObj = data.data.group;
        }
        if (data.data.group === null) {
          groupOrUserObj = data.data.user;
        }
        var isMyContact = false;
        if (groupOrUserObj.hasOwnProperty('is_my_contact')) {
          isMyContact = groupOrUserObj.is_my_contact;
        }
        setIsMyContactAttribute(latestSelectedUser, isMyContact);
        if (data.success && latestSelectedUser === groupOrUserObj.id) {
          if (checkUserStatus(groupOrUserObj)) {
            var userEle = $('#user-' + selectedContactId);
            var template = $.templates('#tmplUserNewStatus');
            var htmlOutput = template.render(groupOrUserObj.user_status);
            userEle.find('.contact-status').html(htmlOutput);
          }
          var conversations = data.data.conversations.reverse();
          selectedContactId = groupOrUserObj.id;
          selectedContactImg = groupOrUserObj.photo_url;
          $('#user-' + selectedContactId).find('.user-avatar-img').attr('src', selectedContactImg);
          var chatHeader = prepareChatHeader(groupOrUserObj, conversations);
          var conversation = chatHeader;
          /** Do not show chat input box if user is blocked */
          if (!groupOrUserObj.hasOwnProperty('is_blocked') || !groupOrUserObj.is_blocked) {
            conversation += chatSendArea;
          } else {
            conversation += hdnTextMessage;
          }

          // When User click on groups members then perform below action
          if ($('.chat__people-body').find('.chat__person-box--active').length <= 0) {
            var isUserElePresent = $('.chat__people-body').find('#user-' + groupOrUserObj.id).length;
            if (!isUserElePresent) {
              var newUserEle = prepareNewConversation(groupOrUserObj.id, groupOrUserObj.name, '', groupOrUserObj.photo_url, groupOrUserObj.is_online, 0);
              chatPeopleBodyEle.prepend(newUserEle);
              $('#user-' + groupOrUserObj.id).addClass('chat__person-box--active');
            }
          }
          if (groupOrUserObj.hasOwnProperty('is_super_admin') && groupOrUserObj.is_super_admin) {
            $('.chat-profile__switch-checkbox').addClass('d-none');
          } else {
            $('.chat-profile__switch-checkbox').removeClass('d-none');
          }
          $('.chat__area-wrapper').html(conversation);
          if (!groupOrUserObj.hasOwnProperty('is_blocked') || groupOrUserObj.is_blocked) {
            $('.contact-title-status').hide();
            $('.contact-status').hide();
          }
          if (isGroup === 0 && !isMyContact && groupOrUserObj.is_req_send_receive) {
            var chatRequest = data.data.chat_request;
            var chatReqObj = groupOrUserObj;
            chatReqObj.chat_req = chatRequest;
            if (chatRequest.from_id == loggedInUserId) {
              console.log('set');
              setSendChatReqTemplate(chatReqObj);
              $('.chat__area-text').addClass('d-none');
              $('.typing').addClass('d-none');
              return false;
            } else {
              console.log('rec');
              groupOrUserObj.chat_req_id = chatRequest.id;
              setReceiveChatReqTemplate(chatReqObj);
              $('.chat__area-text').addClass('d-none');
              $('.typing').addClass('d-none');
              return false;
            }
          }
          if (isGroup === 0 && !isMyContact && groupOrUserObj.privacy == 0) {
            var _chatReqObj = groupOrUserObj;
            _chatReqObj.chat_req = null;
            setSendChatReqTemplate(_chatReqObj);
            $('.chat__area-text').addClass('d-none');
            $('.typing').addClass('d-none');
            return false;
          }
          noConversationEle.hide();
          var firstUnreadEle = $('.chat-conversation .unread').first();
          firstUnreadEle.before(newMessageIndicator);
          if (!isGroup) {
            fireReadMessageEvent(conversations);
          } else {
            var unreadIds = getUnreadGroupMessageIds(conversations);
            fireReadMessageEventUsingIds(unreadIds);
          }
          if (firstUnreadEle.length > 0) {
            scrollAtEle(firstUnreadEle);
          } else {
            shouldCallApiBottom = false;
            var lastEle = $('.chat-conversation').children().last();
            scrollAtEle(lastEle);
          }
          data.data.conversations.length === 0 ? addNoMessagesIndicator() : '';
          if (isGroup) {
            setGroupProfileData(groupOrUserObj, data.data.media);
            closeDropDown();
            if (!groupOrUserObj.removed_from_group) {
              listenForGroupMessageTyping(groupOrUserObj.id);
            }
            loadTooltip();
          } else {
            setUserProfileData(groupOrUserObj, data.data.media);
          }
          getMessageByScroll(isGroup);
          setOpenProfileEvent();
          setOpenMessageSearchEvent();
          setOpenMsgInfoEvent();
          textMessageEle = $('#textMessage');
          textMessageEle.emojioneArea({
            saveEmojisAs: 'shortname',
            autocomplete: true,
            textcomplete: {
              maxCount: 15,
              placement: 'top'
            },
            events: {
              focus: function focus(editor) {
                placeCaretAtEnd(editor[0]);
              }
            }
          });
          if (lastDraftMsg !== '' && lastDraftMsg !== null) {
            isDraftMsg = 1;
            textMessageEle[0].emojioneArea.setText(lastDraftMsg);
            removeLocalStorageItem('user_' + selectedContactId);
            $('#btnSend').removeClass('chat__area-send-btn--disable');
          }
          textMessageEle.data('emojioneArea').setFocus();
          if (isGroup && groupOrUserObj.group_type === 2 && groupOrUserObj.my_role !== 2 || groupOrUserObj.removed_from_group) {
            // Group type is close then only admin can send message
            //hide inputs
            $('.chat__area-text').remove();
          }
          sendMessage();
          groupMembersInfo = [];
          conversationMessages = [];
          currentConversationGroupId = '';
          if (isGroup) {
            currentConversationGroupId = data.data.group.id;
            prepareMessageReadInfo(data.data.conversations, data.data.group.users);
          } else {
            prepareMessageReadInfo(data.data.conversations, []);
          }
        }
        if (getLocalStorageItem('reply')) {
          var replyData = JSON.parse(getLocalStorageItem('reply'));
          if (replyData.user_id == selectedContactId) {
            prepareReplyBox(replyData);
          }
        }
        if (!isGroup && data.data.user.is_blocked_by_auth_user) {
          $('.chat__area-wrapper').append(blockedMessageText);
        } else if (!isGroup && data.data.user.is_blocked && !data.data.user.is_blocked_by_auth_user) {
          $('.chat__area-wrapper').append(blockedMessageText);
          $('.blocked-message-text span').text('You are blocked by this user.');
        }
        updateUnreadMessageCount(1);
        readNotificationWhenOpenChatWindow(selectedContactId);
        loadTooltip();
      },
      error: function error(_error4) {
        console.log(_error4);
      }
    });
    $('.chat__area-wrapper').on('dragover', function (event) {
      event.preventDefault();
      event.stopPropagation();
      $('#fileUpload').modal('show');
    });
  });
  window.closeDropDown = function () {
    $(document).on('click', '.chat-profile', function () {
      var dropDownEle = $("#nav-group-members").find("[aria-expanded='true']");
      if (dropDownEle.length) {
        dropDownEle.trigger('click');
      }
    });
  };
  window.prepareMessageReadInfo = function (messages, members) {
    $.each(members, function (index, val) {
      if (val.id != loggedInUserId) {
        var _data = {
          'user_id': val.id,
          'name': val.name,
          'photo_url': val.photo_url
        };
        groupMembersInfo[val.id] = _data;
      }
    });
    $.each(messages, function (index, val) {
      conversationMessages.push(val);
    });
  };
  window.checkReqAlreadySent = function (chatReqObj) {
    if (chatReqObj) {
      return chatReqObj.status == 0 ? true : false;
    }
    return false;
  };
  window.checkReqAlreadyDeclined = function (chatReqObj) {
    if (chatReqObj) {
      return chatReqObj.status == 2 ? true : false;
    }
    return false;
  };
  window.setSendChatReqTemplate = function (user) {
    var template = $.templates('#sendRequestTmpl');
    var myHelpers = {
      checkReqAlreadySent: checkReqAlreadySent
    };
    var htmlOutput = template.render(user, myHelpers);
    $('#conversation-container').html(htmlOutput);
  };
  window.setReceiveChatReqTemplate = function (user) {
    var template = $.templates('#getChatRequestTmpl');
    var myHelpers = {
      checkReqAlreadyDeclined: checkReqAlreadyDeclined
    };
    var htmlOutput = template.render(user, myHelpers);
    $('#conversation-container').html(htmlOutput);
  };
  window.setIsMyContactAttribute = function (userId, isMyContact) {
    var conversationEle = $('#user-' + userId);
    var isGroup = conversationEle.data('is_group');
    if (isGroup === 1) {
      conversationEle.attr('data-is_my_contact', true);
      return true;
    }
    conversationEle.attr('data-is_my_contact', isMyContact);
  };
  $(document).on('click', '#sendChatRequest', function (e) {
    var userId = $(this).data('id');
    var data = {
      'to_id': userId,
      'message': $('#chatRequestMessage-' + userId).val()
    };
    $.ajax({
      type: 'POST',
      url: route('send-chat-request'),
      data: data,
      success: function success(data) {
        if (data.success) {
          displayToastr('success', 'success', data.message);
          $('#chatRequestMessage-' + userId).val('');
          $('.request__content-title').text('You have send request to this user.');
          $('.send__request__message').hide();
        }
      },
      error: function error(_error5) {
        displayToastr('error', 'error', _error5.responseJSON.message);
      }
    });
  });
  $(document).on('click', '#acceptChatReq', function (e) {
    var chatReqId = $(this).data('id');
    var data = {
      'id': chatReqId
    };
    $.ajax({
      type: 'POST',
      url: route('accept-chat-request'),
      data: data,
      success: function success(data) {
        if (data.success) {
          displayToastr('success', 'success', data.message);
          setTimeout(function () {
            isAccepted = true;
            $('#user-' + data.data.from_id).click();
          }, 500);
        }
      },
      error: function error(_error6) {
        displayToastr('error', 'error', _error6.responseJSON.message);
      }
    });
  });
  $(document).on('click', '#declineChatReq', function (e) {
    var chatReqId = $(this).data('id');
    var data = {
      'id': chatReqId
    };
    $.ajax({
      type: 'POST',
      url: route('decline-chat-request'),
      data: data,
      success: function success(data) {
        if (data.success) {
          displayToastr('success', 'success', data.message);
          $('#user-' + data.data.from_id).find('.chat__person-box-count').addClass('d-none').text(0);
          $('#user-' + data.data.from_id).trigger('click');
        }
      },
      error: function error(_error7) {
        displayToastr('error', 'error', _error7.responseJSON.message);
      }
    });
  });
  function placeCaretAtEnd(el) {
    if (!placeCaret) {
      return;
    }
    if (typeof window.getSelection != 'undefined' && typeof document.createRange != 'undefined') {
      var range = document.createRange();
      range.selectNodeContents(el);
      range.collapse(false);
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(range);
    } else if (typeof document.body.createTextRange != 'undefined') {
      var textRange = document.body.createTextRange();
      textRange.moveToElementText(el);
      textRange.collapse(false);
      textRange.select();
    }
  }

  // $(document).on('click', '[data-toggle="lightbox"]', function (event) {
  //     event.preventDefault()
  //     $(this).ekkoLightbox()
  //     setTimeout(function () {
  //         let ekkoLightBoxModal = $(document).
  //             find('.ekko-lightbox.modal.fade.show')
  //         ekkoLightBoxModal.find('div .modal-content').
  //             prepend(
  //                 `<div class="modal-header"><button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>`)
  //     }, 500)
  // })

  // $(document).on('click', '[data-bs-toggle="media"]', function (event) {
  //     event.preventDefault()
  //     $(this).ekkoLightbox()
  //     setTimeout(function () {
  //         let ekkoLightBoxModal = $(document).
  //             find('.ekko-lightbox.modal.fade.in.show')
  //         ekkoLightBoxModal.find('div .modal-content').
  //             prepend(
  //                 `<div class="modal-header"><button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>`)
  //     }, 500)
  // })

  $('body').tooltip({
    selector: '.profile-media',
    placement: 'top',
    boundary: 'window',
    trigger: 'hover'
  });
  function setUserProfileData(user) {
    var media = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
    $('.chat-profile').empty();
    var template = $.templates('#tmplUserDetails');
    var myHelpers = {
      prepareMedia: prepareMedia,
      getCalenderFormatForLastSeen: getCalenderFormatForLastSeen,
      disabledIfReported: disabledIfReported,
      groupUsers: groupUsers
    };
    user.media = media;
    var htmlOutput = template.render(user, myHelpers);
    $('.chat-profile').html(htmlOutput);
  }
  window.groupUsers = function (users) {
    var groupUsers = ['You'];
    $.each(users, function (index, val) {
      if (val.id != loggedInUserId) {
        groupUsers.push(val.name);
      }
    });
    return groupUsers.join(', ');
  };
  window.disabledIfReported = function (reportedUser) {
    if (reportedUser == null) {
      return '';
    }
    return 'disabled';
  };
  $(document).on('click', '#open-report-user-modal', function () {
    var userId = $(this).attr('data-id');
    $('#reportUserId').val(userId);
    $('#reportUserNote').val('');
    $('#reportUserValidationErrorsBox').hide().text('');
    $('#reportUserModal').modal('show');
  });
  $(document).on('keyup', '#reportUserNote', function () {
    $('#reportUserValidationErrorsBox').hide().text('');
  });
  $(document).on('click', '#reportUser', function () {
    var loadingButton = $(this);
    var userId = $("#reportUserId").val();
    var reportUserNote = $("#reportUserNote").val();
    if (reportUserNote === '') {
      $('#reportUserValidationErrorsBox').show().text('The notes field is required.');
      return false;
    }
    loadingButton.button('loading');
    var data = {
      'reported_to': userId,
      'notes': reportUserNote
    };
    $.ajax({
      type: 'POST',
      url: route('report-user.store'),
      data: data,
      success: function success(data) {
        $('#open-report-user-modal').prop('disabled', true);
        displayToastr('Success', 'success', data.message);
        $('#reportUserModal').modal('hide');
      },
      error: function error(data) {
        $('#reportUserValidationErrorsBox').show().text(data.responseJSON.message);
      },
      complete: function complete() {
        loadingButton.button('reset');
      }
    });
  });
  window.displaySideMedia = function (mediaObj) {
    $('.no-photo-found').hide();
    var mediaHtml = prepareMedia(mediaObj);
    $('.chat-profile__media-container').append(mediaHtml);
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  };

  // move chat conversation to last messages
  function scrollToLastMessage() {
    var isScrollLast = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
    scrollAtLastMsg = true;
    var chatConversation = $('.chat-conversation');
    var height = chatConversation.prop('scrollHeight');
    if (!isScrollLast) {
      height = height / 2;
    }
    chatConversation.scrollTop(height);
  }
  window.prepareMedia = function (data) {
    if (data.message_type === mediaTypeImage) {
      return imageRendererInSideMedia(data.message, data.id);
    } else if (data.message_type === mediaTypePdf) {
      return sideMediaRenderer(data.message, data.file_name, 'fa-file-pdf-o', data.id);
    } else if (data.message_type === mediaTypeDocx) {
      return sideMediaRenderer(data.message, data.file_name, 'fa-file-word-o', data.id);
    } else if (data.message_type === mediaTypeVideo) {
      return sideMediaRenderer(data.message, data.file_name, 'fa-file-video-o', data.id);
    } else if (data.message_type === youtubeUrl) {
      return sideMediaRenderer(data.message, data.message, 'fa fa-youtube-play', data.id);
    } else if (data.message_type === mediaTypeTxt) {
      return sideMediaRenderer(data.message, data.file_name, 'fa-file-text-o', data.id);
    } else if (data.message_type === mediaTypeXls) {
      return sideMediaRenderer(data.message, data.file_name, 'fa-file-excel-o', data.id);
    } else if (data.message_type === mediaTypeVoice) {
      return sideMediaRenderer(data.message, data.file_name, 'fa-file-audio-o', data.id);
    } else if (data.message_type === mediaTypeZip) {
      return sideMediaRenderer(data.message, data.file_name, 'fa fa-file-archive-o', data.id);
    } else {
      return '';
    }
  };
  function setGroupProfileData(group, media) {
    currentSelectedGroupId = group.id;
    var template = $.templates('#tmplGroupDetails');
    var myHelpers = {
      prepareMedia: prepareMedia,
      getLocalDate: getLocalDate,
      checkUserStatusForGroupMember: checkUserStatusForGroupMember
    };
    group.media = media;
    group.logged_in_user_id = parseInt(loggedInUserId);
    group.members_count = group.users.length;
    membersCountArr[group.id] = group.users.length;
    myRoleInGroup = group.my_role;
    groupMembers = pluck(group.users, 'id');
    var htmlOutput = template.render(group, myHelpers);
    $('.chat-profile').empty();
    $('.chat-profile').html(htmlOutput);
  }
  function pluck(objs, name) {
    var sol = [];
    for (var i in objs) {
      if (objs[i].hasOwnProperty(name)) {
        // console.log(objs[i][name]);
        sol.push(objs[i][name]);
      }
    }
    return sol;
  }
  window.scrollTop = function () {
    var ele = $('.chat-conversation');
    ele.scrollTop(50);
  };
  window.scrollAtEle = function (element) {
    if (element.length > 0) {
      var ele = $('.chat-conversation');
      var position = element.position();
      ele.scrollTop(position.top - 100);
    }
  };
  var chatProfileEle = $('.chat-profile');
  function setOpenProfileEvent() {
    $('.open-profile-menu, .chat-profile__close-btn').on('click', function (e) {
      profileToggle();
      if ($(this).parents('.dropdown-menu').length) {
        $(this).parents('.dropdown-menu').removeClass('show');
      }
      //added this bcz after this, it will not consider document click event and will not close profile
      e.stopPropagation();
    });
  }
  $(document).on('click', '.ekko-lightbox', function (e) {
    // using this when we click anywhere (outside if image modal also), it will not consider document click event so profile and conversation-list sidebar will remain as it was before opening image
    // $(document).on('click', '.ekko-lightbox-nav-overlay', function (e) { // if want to do same with only arraow of image than use this class
    e.stopPropagation();
  });
  window.profileToggle = function () {
    if (chatProfileEle.hasClass('chat-profile--active')) {
      chatProfileEle.removeClass('chat-profile--active').addClass('chat-profile--out');
      setTimeout(function () {
        chatProfileEle.toggle();
      }, 300);
    } else {
      closeMsgInfo();
      chatProfileEle.addClass('chat-profile--active').removeClass('chat-profile--out').toggle();
    }
  };
  window.closeProfileInfo = function () {
    if (chatProfileEle.hasClass('chat-profile--active')) {
      chatProfileEle.removeClass('chat-profile--active').addClass('chat-profile--out');
      chatProfileEle.toggle();
    }
  };
  var msgInfoEle = $('.msg-info');
  window.setOpenMsgInfoEvent = function () {
    $('.open-msg-info').on('click', function (e) {
      var messageId = $(this).attr('data-message-id');
      var isGroup = $(this).attr('data-is_group');
      if (isGroup === '1' && typeof messageId != 'undefined') {
        setReadByContactsInfo(messageId);
      } else {
        setReadByMessageInfo(messageId);
      }
      openMsgInfo();
      e.stopPropagation();
    });
    $('.msg-info__close-btn').on('click', function (e) {
      closeMsgInfo();
      e.stopPropagation();
    });
  };
  window.openMsgInfo = function () {
    closeProfileInfo();
    if (!msgInfoEle.hasClass('msg-info--active')) {
      msgInfoEle.addClass('msg-info--active').removeClass('msg-info--out').show();
    }
  };
  window.closeMsgInfo = function () {
    if (msgInfoEle.hasClass('msg-info--active')) {
      msgInfoEle.removeClass('msg-info--active').addClass('msg-info--out');
      msgInfoEle.hide();
    }
  };
  window.setReadByContactsInfo = function (messageId) {
    var messageInfo = getMsgInfoFromconversationMessages(messageId);
    if (messageInfo !== null) {
      showMessageInMessageInfo(messageInfo);
      showConversationInfo(messageInfo);
      setReadByUsersCount();
    }
  };
  window.setReadByUsersCount = function () {
    var readByUsersCount = $("#read-by-users").find('.chat__person-box').length;
    $("#read-by-users-section").find('.group-users-count').text('(' + readByUsersCount + ')');
  };
  window.setReadByMessageInfo = function (messageId) {
    var messageInfo = getMsgInfoFromconversationMessages(messageId);
    if (messageInfo !== null) {
      showSingleConversationInfo(messageInfo);
    }
  };
  window.checkReadAtDate = function (readAt) {
    return readAt == null || readAt == '' || readAt == '0000-00-00 00:00:00' ? false : true;
  };
  window.prepareReadByContactsInfoHtml = function (readByUsers) {
    var template = $.templates('#groupMsgReadUnreadInfo');
    var helpers = {
      getCalenderFormatForLastSeen: getCalenderFormatForLastSeen,
      checkReadAtDate: checkReadAtDate
    };
    return template.render(readByUsers, helpers);
  };
  var remainingUsersEle = $('#remaining-users');
  var remainingUsersListEle = $('#remaining-users-list');
  var remainingUsersSectionEle = $('#remaining-users-section');
  var remainingUsersDividerEle = $('#remaining-users-divider');
  var readByUsersEle = $('#read-by-users');
  var readByUsersDivider = $('#read-by-users-divider');
  var readByUsersSection = $('#read-by-users-section');
  var singleMsgDivider = $('#single-msg-divider');
  var singleMsgSection = $('#single-msg-section');
  window.showConversationInfo = function (messageInfo) {
    var readByUsers = [];
    var remainingUsers = [];
    if (messageInfo !== null) {
      showMessageInMessageInfo(messageInfo);
      $.each(messageInfo.read_by, function (index, val) {
        val.user = groupMembersInfo[val.user_id];
        if (val.read_at != null) {
          readByUsers.push(val);
        } else {
          remainingUsers.push(val);
        }
      });
    }
    resetReadByUsers();
    if (remainingUsers.length > 0) {
      remainingUsersEle.text(remainingUsers.length + ' remaining').attr('data-remaining_count', remainingUsers.length);
      var remainingUsersHtml = prepareReadByContactsInfoHtml(remainingUsers);
      remainingUsersListEle.html(remainingUsersHtml);
      remainingUsersDividerEle.show();
      remainingUsersSectionEle.show();
    }
    readByUsersEle.attr('class', 'message-' + messageInfo.id + '-read-by-users');
    readByUsersEle.html('');
    if (readByUsers.length > 0) {
      var readByUsersHtml = prepareReadByContactsInfoHtml(readByUsers);
      readByUsersEle.html(readByUsersHtml);
      readByUsersDivider.show();
      readByUsersSection.show();
    }
  };
  window.showSingleConversationInfo = function (messageInfo) {
    if (messageInfo !== null) {
      remainingUsersDividerEle.show();
      readByUsersDivider.show();
      singleMsgSection.show().html('');
      showMessageInMessageInfo(messageInfo);
      var helpers = {
        getCalenderFormatForLastSeen: getCalenderFormatForLastSeen
      };
      var template = $.templates('#singleMessageReadInfoTmpl');
      var msgHtml = template.render(messageInfo, helpers);
      $("#single-msg-section").html(msgHtml);
    }
    resetSingleMessageReadByInfo();
  };
  window.resetSingleMessageReadByInfo = function () {
    remainingUsersSectionEle.hide();
    readByUsersSection.hide();
  };
  window.resetReadByUsers = function () {
    remainingUsersEle.text('').attr('data-remaining_count', 0);
    remainingUsersListEle.html('');
    remainingUsersDividerEle.hide();
    remainingUsersSectionEle.hide();
    readByUsersEle.html('');
    readByUsersDivider.hide();
    readByUsersSection.hide();
    singleMsgDivider.hide();
    singleMsgSection.hide().html('');
  };
  window.getMsgInfoFromconversationMessages = function (messageId) {
    var messageInfo = null;
    $.each(conversationMessages, function (index, val) {
      if (val.id == messageId) {
        messageInfo = val;
        return false;
      }
    });
    return messageInfo;
  };
  window.showMessageInMessageInfo = function (messageInfo) {
    var templateData = {};
    var helpers = {
      displayMessage: displayMessage,
      getChatMagTimeInConversation: getChatMagTimeInConversation
    };
    var template = $.templates('#groupMsgReadUnreadMessage');
    templateData.data = messageInfo;
    templateData.loggedInUserId = loggedInUserId;
    var msgHtml = template.render(templateData, helpers);
    $("#msg-info-container-msg").html(msgHtml);
  };
  $(document).on('click', function () {
    //by clicking anywhere in document profile or chat side bar if any will present than it will close
    if (chatProfileEle.hasClass('chat-profile--active')) {
      chatProfileEle.removeClass('chat-profile--active').addClass('chat-profile--out');
      setTimeout(function () {
        chatProfileEle.toggle();
      }, 300);
    }
    $('.chat__people-wrapper-bar').addClass('fa-bars').removeClass('fa-times');
    // $('.chat__people-wrapper').
    //     addClass('chat__people-wrapper--responsive');
  });

  if (window.matchMedia('(max-width: 575px)').matches) {
    $(document).on('click', '.contact-area, .chat__people-close-bar, .chat__person-box--active', function () {
      if (!$('.chat__people-wrapper').hasClass("chat__people-wrapper--responsive")) {
        $('.chat__people-wrapper').addClass('hide-sidebar');
        $('.chat__people-wrapper').addClass('chat__people-wrapper--responsive');
        $('.chat__people-wrapper').removeClass('show-sidebar');
      }
    });
    $(document).on('click', '.back-to-chat', function () {
      if ($('.chat__people-wrapper').hasClass("chat__people-wrapper--responsive")) {
        $('.chat__people-wrapper').addClass('show-sidebar');
        $('.chat__people-wrapper').removeClass('hide-sidebar');
        $('.chat__people-wrapper').removeClass('chat__people-wrapper--responsive');
      }
    });
  }
  $(document).on('click', '.chat-profile', function (e) {
    //to prevent click event of this class bcz by click of this class profile does not close
    e.stopPropagation();
  });
  $(document).on('click', '.chat__people-wrapper', function (e) {
    //to prevent click event of this class bcz by click of this class chat side bar does not close
    e.stopPropagation();
  });
  window.setOpenMessageSearchEvent = function () {
    // search msg opacity transition
    $(document).on('focus', '.chat__area-action-search-input', function () {
      $('.chat__search--conversation').css({
        'opacity': '1'
      });
    });

    // search msg opacity transition
    $('.chat__area-action-search-input').on('blur', function () {
      $('.chat__search--conversation').css({
        'opacity': '.5'
      });
    });

    // responsive search bar
    $('.open-search-bar').on('click', function () {
      $('.chat__area-action').addClass('chat__area-action--open');
      // height increased
      $('.chat__area-header').addClass('chat__area-header--active');
      $('.chat__area-action-search-input').focus();
    });

    // close search bar
    $('.chat__area-action-search-close').on('click', function () {
      $('.chat__area-action').removeClass('chat__area-action--open');
      // height initial
      $('.chat__area-header').removeClass('chat__area-header--active');
    });
  };

  //responsive chat side bar
  $('.chat__people-wrapper-bar').on('click', function (e) {
    $(this).toggleClass('fa-bars fa-times');
    $('.chat__people-wrapper').toggleClass('chat__people-wrapper--responsive');
    //added this bcz after this, it will not consider document click event and will not close chat side bar
    e.stopPropagation();
  });

  // responsive serach icon
  $('.chat__search-responsive-icon').on('click', function () {
    $('.chat__people-wrapper').removeClass('chat__people-wrapper--responsive');
    $('.chat__people-wrapper-bar').toggleClass('fa-bars fa-times');
    $('.chat__search-input').focus();
  });

  // hamburger menu
  $('#nav-icon3').on('click', function () {
    $(this).toggleClass('open');
    $('.chat__people-wrapper').toggleClass('chat__people-wrapper--responsive');
  });
  $('.chat__chat-contact-item').on('click', function () {
    $('.chat__chat-contact .chat__chat-contact-item').removeClass('chat__chat-contact--active');
    $(this).addClass('chat__chat-contact--active');
  });
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
  var data = '';
  window.checkIsArchiveChat = function () {
    return $('.chat__person-box--active').parents('#archivePeopleBody').length;
  };
  window.moveConversationFromArchiveToActiveChat = function () {
    var chatEle = $('.chat__person-box--active');
    chatEle.find('.chat__person-box-archive').text('Archive Chat');
    $('#chatPeopleBody').prepend(chatEle);
    $('#archivePeopleBody').find('.chat__person-box--active').remove();
    makeActiveChatTabActive();
  };
  function sendMessage() {
    var previousInput = '';
    var newInput = '';
    var toId = $('#toId').val();
    var isGroup = $('.chat__person-box--active').data('is_group');
    var isArchiveChat = checkIsArchiveChat();
    data = {
      'to_id': toId,
      '_token': csrfToken,
      'is_archive_chat': isArchiveChat
    };
    if (isGroup) {
      data.is_group = 1;
    }
    $('#btnSend').on('click', function () {
      var message = textMessageEle[0].emojioneArea.getText().trim();
      if (message === '') {
        return false;
      }
      $(this).addClass('chat__area-send-btn--disable');
      data.message = message;
      storeMessage(data);
      resetForm();
      removeLocalStorageItem('user_' + selectedContactId);
      textMessageEle.focus();
    });
    textMessageEle[0].emojioneArea.on('keyup emojibtn.click', function (btn, event) {
      var keyCode = event.keyCode || event.which;
      if (event.type == 'keyup' && keyCode !== 9) {
        textMessageEle[0].emojioneArea.hidePicker();
      }
      var message = textMessageEle[0].emojioneArea.getText().trim();
      newInput = message;
      if (event && event.which === 13) {
        if (newInput !== previousInput && !isDraftMsg) {
          previousInput = newInput;
          return false;
        }
        if (message.length === 0) {
          $('#btnSend').addClass('chat__area-send-btn--disable');
          return;
        } else {
          $('#btnSend').removeClass('chat__area-send-btn--disable');
          isTyping();
        }
        $('#btnSend').addClass('chat__area-send-btn--disable');
        data.message = message;
        storeMessage(data);
        resetForm();
        isDraftMsg = 0;
        removeLocalStorageItem('user_' + selectedContactId);
        return true;
      }
      isDraftMsg = 0;
      if (message.length === 0) {
        $('#btnSend').addClass('chat__area-send-btn--disable');
      } else {
        $('#btnSend').removeClass('chat__area-send-btn--disable');
        isTyping();
      }
      previousInput = newInput;
    });
    textMessageEle[0].emojioneArea.on('blur', function (btn, event) {
      var message = textMessageEle[0].emojioneArea.getText().trim();
      if (message.length > 0) {
        setLocalStorageItem('user_' + toId, message);
      }
    });
  }
  function loadEojiArea() {
    textMessageEle = $('#textMessage');
    textMessageEle.emojioneArea({
      saveEmojisAs: 'shortname',
      autocomplete: true,
      textcomplete: {
        maxCount: 15,
        placement: 'top'
      },
      events: {
        focus: function focus(editor) {
          placeCaretAtEnd(editor[0]);
        }
      }
    });
    textMessageEle.data('emojioneArea').setFocus();
  }
  window.storeMessage = function (reqData) {
    var isMyContact = $('#user-' + selectedContactId).data('is_my_contact');
    reqData.is_my_contact = isMyContact ? 1 : 0;
    var Filter = __webpack_require__(/*! bad-words */ "./node_modules/bad-words/lib/badwords.js"),
      filter = new Filter();
    reqData.message = filter.clean(reqData.message);
    var messageType = 0;
    if ($('.chat__text-preview').length > 0) {
      messageType = $('.chat__text-preview').data('message-type');
    }
    reqData.is_group = 0;
    if (currentConversationGroupId !== '') {
      reqData.is_group = 1;
    }
    var randomMsgId = null;
    if (messageType == 0 && !reqData.file_name) {
      randomMsgId = addMessage(reqData);
    }
    var replay = false;
    if ($('.chat__text-preview').length > 0) {
      var replyTo = $('.chat__text-preview').data('message-id');
      reqData.reply_to = replyTo;
      replay = true;
      removeReplayBox();
    }
    var isArchiveChat = checkIsArchiveChat();
    reqData.is_archive_chat = isArchiveChat;
    if (isArchiveChat) {
      moveConversationFromArchiveToActiveChat();
      showNoArchiveConversationEle();
    }
    $.ajax({
      type: 'POST',
      url: route('conversations.store'),
      data: reqData,
      success: function success(data) {
        reqData.reply_to = '';
        if (data.success === true) {
          fireAddNewContactIdEvent(reqData.to_id);
          var messageData = data.data.message;
          conversationMessages.push(data.data.message);
          // notificationSound();
          $('.msg-options').find('[data-message-id=' + randomMsgId + ']').attr('data-message-id', messageData.id);
          $('.msg-options').find('[data-message-type=' + randomMsgId + ']').attr('data-message-type', messageData.message_type);
          var msgSetInWindow = false;
          if (messageType != 0) {
            msgSetInWindow = true;
            setSentOrReceivedMessage(messageData);
          }
          if (messageData.message_type === 0) {
            $('.chat-conversation').find('[data-message_id=' + randomMsgId + ']').addClass('message-' + messageData.id).attr('data-message_id', messageData.id);
            updateMessageInSenderwindow(messageData);
            if (messageData.url_details) {
              $('.message-' + messageData.id).find('.message').empty();
              $('.message-' + messageData.id).find('.chat-conversation__bubble.clearfix').addClass('max-width-35');
              $('.message-' + messageData.id).find('.message').append(displayMessage(messageData));
            }
          } else {
            if (messageData.message_type === 6) {
              $('.chat-conversation').find('[data-message_id=' + randomMsgId + ']').remove();
            }
            if (!msgSetInWindow) {
              setSentOrReceivedMessage(messageData);
            }
          }
          var toUserEle = chatPeopleBodyEle.find('#user-' + reqData.to_id);
          addUserToTopOfConversation(reqData.to_id, toUserEle);
          setOpenMsgInfoEvent();
        }
        var unreadMessageCount = getSelectedUserUnreadMsgCount(reqData.to_id);
        if (unreadMessageCount > 0) {
          scrollToLastMessage();
        }
      },
      error: function error(_error8) {
        reqData.reply_to = '';
        displayToastr('Error', 'error', _error8.responseJSON.message);
        $('#btnSend').removeClass('chat__area-send-btn--disable');
      }
    });
  };
  window.fireAddNewContactIdEvent = function (userId) {
    if ($.inArray(userId, newConversationStartedUserIds) != -1) {
      window.livewire.emit('addNewContactId', userId);
      newConversationStartedUserIds = jQuery.grep(newConversationStartedUserIds, function (value) {
        return value != userId;
      });
    }
  };
  function addMessage(data) {
    var messageData = data;
    messageData.message = htmlEntities(data.message);
    var currentTime = moment().local().format('hh:mma');
    if (isUTCTimezone == '1') {
      currentTime = getLocalDate(moment().utc());
    }
    messageData.time = currentTime;
    messageData.senderName = authUserName;
    messageData.senderRole = authRole;
    messageData.senderImg = authImgURL;
    messageData.message = getMessageByItsTypeForChatList(messageData.message, 0);
    var randomMsgId = Math.floor(Math.random() * 6) + Date.now();
    messageData.randomMsgId = randomMsgId;
    if ($('.chat__text-preview').length > 0) {
      if ($('.chat__text-preview').find('emojine')) {
        var emojiText = $('.chat__text-preview').find('.replay-message').html();
        messageData.replyMessage = emojiText;
      } else {
        messageData.replyMessage = $('.chat__text-preview').find('.replay-message').text();
      }
      messageData.receiverName = $('.chat__text-preview').find('.reply-to-user').text();
    } else {
      messageData.replyMessage = '';
      messageData.receiverName = '';
    }
    var template = $.templates('#tmplSingleMessage');
    var htmlOutput = template.render(messageData);
    $(function () {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
    var getLastTime = $('.chat__person-box--active').find('.chat__person-box-time').text();

    // Append today's timeline
    var todaysTimeLine = $.templates('#tmplToday');
    if (getLastTime == 'Yesterday' || moment(getLastTime).isValid()) {
      $('.chat__person-box--active').find('.chat__person-box-time').text(messageData.time);
      $('.chat-conversation').append(todaysTimeLine);
    } else if (!getLastTime) {
      $('.chat-conversation').append(todaysTimeLine);
      $('.chat__person-box--active').find('.chat__person-box-time').text(getLocalDate(moment().utc()));
    }
    $('.chat-conversation').append(htmlOutput);
    $('.chat__not-selected').hide();
    scrollToLastMessage();
    setOpenMsgInfoEvent();
    return randomMsgId;
  }
  function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  window.getSelectedUserUnreadMsgCount = function (userId) {
    return $('#user-' + userId).find('.chat__person-box-count').text();
  };
  window.setSentOrReceivedMessage = function (message) {
    var recentChatMessage = prepareChatConversation(message, false);
    var chatConversationEle = $('.chat-conversation');
    var needToScroll = false;
    displaySideMedia(message);
    if (message.from_id == loggedInUserId) {
      //at sender side
      if (isAllMessagesRead) {
        chatConversationEle.append(recentChatMessage);
      }
      scrollToLastMessage();
    } else {
      if (isAllMessagesRead || appendGroupMessagesAtLast) {
        notificationSound();
        var needToScrollCondition = add(chatConversationEle.scrollTop(), chatConversationEle.innerHeight()) >= chatConversationEle[0].scrollHeight - 3;
        needToScroll = needToScrollCondition ? true : false;
        chatConversationEle.append(recentChatMessage);
        if (needToScroll) {
          scrollToLastMessage();
          // fireReadMessageEvent([message]);
        } else {
          var unreadMessageCountEle = $('#user-' + latestSelectedUser).find('.chat__person-box-count');
          var unreadMessageCount = unreadMessageCountEle.text();
          unreadMessageCount = add(unreadMessageCount, 1);
          unreadMessageCountEle.text(unreadMessageCount);
          unreadMessageCountEle.removeClass('d-none');
          var newMsgBadge = chatConversationEle.find('.chat__msg-day-new-msg');
          if (newMsgBadge.length === 0) {
            var firstUnreadEle = $('.message-' + message.id);
            firstUnreadEle.before(newMessageIndicator);
          }
        }
      }
    }
    $('.chat__not-selected').hide();
    noConversationEle.hide();
    updateMessageInSenderwindow(message);
    if (!message.is_group) {
      //update in reciever's conversation
      var userEle = chatPeopleBodyEle.find('#user-' + message.from_id);
      userEle.find('.chat-message').html(getMessageByItsTypeForChatList(message.message, message.message_type, message.file_name));
      userEle.find('.chat__person-box-time').text(getLocalDate(message.created_at));
    }
    loadTooltip();
  };
  window.updateMessageInSenderwindow = function (message) {
    //update in logged in user's conversation
    var userEle = chatPeopleBodyEle.find('#user-' + message.to_id);
    userEle.find('.chat-message').html(getMessageByItsTypeForChatList(message.message, message.message_type, message.file_name));
    userEle.find('.chat__person-box-time').text(getLocalDate(message.created_at));
  };
  function resetForm() {
    textMessageEle[0].emojioneArea.setText('');
  }
  function add(num1, num2) {
    return parseInt(num1) + parseInt(num2);
  }
  function messageReadAfter5Seconds() {
    var newIds = [];
    // prepare unique array of unread messages
    $.each(unreadMessageIds, function (i, v) {
      if ($.inArray(v, newIds) === -1 && $.inArray(v, readedMessageIds) === -1) {
        newIds.push(v);
      }
    });
    readedMessageIds = $.merge(readedMessageIds, newIds);
    if (newIds.length <= 0) {
      return;
    }
    var isGroup = $('.chat__person-box--active').data('is_group');
    var groupId = isGroup ? $('.chat__person-box--active').data('id') : '';
    unreadMessageIds = []; // make unread message ids empty
    var senderId = $('.chat__person-box--active').data('id');
    $.ajax({
      type: 'post',
      url: route('read-message'),
      data: {
        ids: newIds,
        '_token': csrfToken,
        'is_group': isGroup,
        'group_id': groupId
      },
      success: function success(data) {
        $.each(newIds, function (index, value) {
          $('.message-' + value).removeClass('unread');
        });
        if (data.success == true) {
          var remainingUnread = data.data.remainingUnread;

          // TODO: do not update unread messages count when chat window is  open
          var UnreadCountEle = $('#user-' + senderId).find('.chat__person-box-count');
          UnreadCountEle.text(remainingUnread);
          if (remainingUnread > 0) {
            isAllMessagesRead = false;
            callAfterAPI = true;
            // TODO : do not scroll again and again
            // Scroll to minor top from bottom so don't need to again scroll bottom for load new messages
            // $('.chat-conversation').scrollTop(
            //     $('.chat-conversation').scrollTop() - 100);
          } else {
            UnreadCountEle.text(0).addClass('d-none');
            setTimeout(function () {
              $('.chat__msg-day-new-msg').parent().remove();
            }, 20000);
            scrollAtLastMsg = false;
            isAllMessagesRead = true;
            callAfterAPI = false;
          }
        }
      }
    });
  }
  window.getUnreadMessageIds = function (conversations) {
    var ids = [];
    $.each(conversations, function (index, conversation) {
      if (conversation.to_id == loggedInUserId && !conversation.status) {
        ids.push(conversation.id);
      }
    });
    return ids;
  };
  window.getUnreadGroupMessageIds = function (conversations) {
    var limit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 10;
    var ids = [];
    var count = 1;
    if (conversations.length > limit) {
      return ids;
    }
    $.each(conversations, function (index, conversation) {
      if (count <= limit && $('.message-' + conversation.id).hasClass('unread')) {
        ids.push(conversation.id);
      }
      count += 1;
    });
    return ids;
  };
  window.fireReadMessageEvent = function (conversations) {
    var unreadMessageIds = getUnreadMessageIds(conversations);
    fireReadMessageEventUsingIds(unreadMessageIds);
  };
  window.fireReadMessageEventUsingIds = function (ids) {
    if (ids.length > 0) {
      // Store unread message ids into global variables
      unreadMessageIds = $.merge(unreadMessageIds, ids);

      // Now call the read message looping function which will check unread message at each 5 seconds
      if (!readMessageFunctionInterval) {
        var interval = setInterval(messageReadAfter5Seconds, 5000); // readMessageFunctionInterval = true;
        readMessageFunctionInterval = true;
      }
    } else {
      isAllMessagesRead = true;
    }
  };
  window.getMessageByItsTypeForChatList = function (message, message_type) {
    var file_name = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
    if (message_type === mediaTypeImage) {
      return '<i class="fa fa-camera" aria-hidden="true"></i>' + ' Photo';
    } else if (message_type === mediaTypePdf) {
      return '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else if (message_type === mediaTypeDocx) {
      return '<i class="fa fa-file-word-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else if (message_type === mediaTypeVoice) {
      return '<i class="fa fa-file-audio-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else if (message_type === mediaTypeVideo) {
      return '<i class="fa fa-file-video-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else if (message_type === mediaTypeTxt) {
      return '<i class="fa fa-file-text-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else if (message_type === mediaTypeXls) {
      return '<i class="fa fa-file-excel-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else if (message_type === mediaTypeZip) {
      return '<i class="fa fa-file-archive-o" aria-hidden="true"></i>' + ' ' + file_name;
    } else {
      return emojione.shortnameToImage(message);
    }
  };
  window.checkUserStatus = function (contactDetail) {
    return contactDetail.hasOwnProperty('user_status') && contactDetail.user_status != null ? true : false;
  };
  window.checkUserStatusForGroupMember = function (userStatus) {
    return userStatus != null ? true : false;
  };
  window.getDraftMessage = function (contactId) {
    var lastDraftMsg = getLocalStorageItem('user_' + contactId);
    return lastDraftMsg != null ? '<i class="fa fa-pencil" aria-hidden="true"></i> ' + getMessageByItsTypeForChatList(lastDraftMsg, 0) : false;
  };
  window.prepareContacts = function (contact) {
    var contactDetail = contact.is_group ? contact.group : contact.user;
    var contactId = contact.is_group ? contact.group_id : contact.user_id;
    var showStatus = true;
    var showUserStatus = false;
    if (contact.is_group && contactDetail.removed_from_group) {
      contactDetail = contact.group_details;
    }
    if ($.inArray(contactId.toString(), myContactIds) != -1) {
      showUserStatus = true;
    }
    if (contact.user != null && $.inArray(parseInt(contactId), blockedUsersList) != -1 || contact.is_group) {
      showStatus = false;
      showUserStatus = false;
    }
    var template = $.templates('#tmplConversationsList');
    var helpers = {
      getMessageByItsTypeForChatList: getMessageByItsTypeForChatList,
      getLocalDate: getLocalDate,
      checkUserStatus: checkUserStatus,
      checkForMyContact: checkForMyContact,
      getDraftMessage: getDraftMessage
    };
    var data = {
      showStatus: showStatus,
      showUserStatus: showUserStatus,
      contactId: contactId,
      contact: contact,
      contactDetail: contactDetail,
      is_online: !contact.is_group ? contact.user.is_online : 0
    };
    var contactElementHtml = template.render(data, helpers);
    if (contact.unread_count > 0) {
      totalUnreadConversations += 1;
      updateUnreadMessageCount(0);
    }
    return contactElementHtml;
  };

  //add latest messaged user to top of conversation
  window.addUserToTopOfConversation = function (userId, userEle) {
    chatPeopleBodyEle.remove('#user-' + userId);
    chatPeopleBodyEle.prepend(userEle);
  };
  window.Echo["private"]("user.".concat(loggedInUserId)).listen(".Modules\\Chat\\Events\\UserEvent", function (e) {
    if (e.type === 1) {
      // block-unblock user event
      blockUnblockUserEvent(e);
    } else if (e.type === 2) {
      // new user-to-user message arrived
      newMessageArrived(e);
    } else if (e.type === 3) {
      // added to group
      window.livewire.emit('searchGroup');
      prepareNewGroupChatConversation(e);
    } else if (e.type === 4) {
      // private message read
      privateMessageReadByUser(e);
    } else if (e.type === 5) {
      // message deleted for everyone
      messageDeletedForEveryone(e);
    } else if (e.type === 6 && getCurrentUserId() === e.owner_id) {
      readNotificationWhenChatWindowOpen(e.id, '#owner-' + e.owner_id);
    } else if (e.type === 7) {
      //chat request arrive
      if (!checkIsMyContact(e)) {
        return false;
      }
      newMessageArrived(e);
    } else if (e.type === 8) {
      //chat request accepted
      myContactIds.push(e.owner_id);
      updateMyContactWhenChatReqAccepted(e.owner_id);
      if (getCurrentUserId() == e.owner_id) {
        $('#user-' + getCurrentUserId()).trigger('click');
      }
    }
  });
  window.updateMyContactWhenChatReqAccepted = function (userId) {
    $('#chatPeopleBody').find('#user-' + userId).attr('data-is_my_contact', 1);
  };
  window.checkIsMyContact = function (messageData) {
    if (messageData.is_group == 1) {
      return true;
    }
    var userEle = $('#user-' + selectedContactId);
    if (userEle.length > 0) {
      var isMyContact = userEle.attr('data-is_my_contact');
      return isMyContact == '1' ? true : false;
    }
    return true;
  };
  function messageDeletedForEveryone(e) {
    var messageEle = $('.message-' + e.id);
    removeTimeline(messageEle);
    updateMessageOnReceiverDrawer(e.previousMessage, messageEle, e);
    if (chatPeopleBodyEle.find('#user-' + e.from_id).length) {
      // if chat window is open
      $('.message-' + e.id).remove();
    }
  }
  function updateMessageOnReceiverDrawer(previousMessage, messageEle, delMsgInfo) {
    var userEle = $(document).find('#user-' + delMsgInfo.from_id);
    if (previousMessage != null && messageEle.nextAll('#send-receive-direction:first').length === 0) {
      var chatPersonBox = userEle;
      var oldMsgCount = userEle.find('.chat__person-box-count').text();
      oldMsgCount = parseInt(oldMsgCount);
      if (oldMsgCount > 1) {
        var msgCount = oldMsgCount - 1;
        chatPersonBox.find('.chat__person-box-count').html(msgCount);
      } else {
        chatPersonBox.find('.chat__person-box-count').html(0).removeClass('d-none').addClass('d-none');
      }
      chatPersonBox.find('.chat-message').html(getMessageByItsTypeForChatList(previousMessage.message, previousMessage.message_type, previousMessage.file_name));
      chatPersonBox.find('.chat__person-box-time').text(getLocalDate(previousMessage.created_at));
    } else if (delMsgInfo.previousMessage == null && userEle.length > 0) {
      userEle.find('.chat-message').html('');
      userEle.find('.chat__person-box-count').text(0).addClass('d-none');
    }
    checkAllMsgAndShowNoMsgYet();
  }
  function privateMessageReadByUser(e) {
    var readClass = 'chat-container__read-status--read';
    var unreadClass = 'chat-container__read-status--unread';
    $.each(e.ids, function (i, v) {
      $('.message-' + v).find('.chat-container__read-status').removeClass(unreadClass).addClass(readClass);
      updateReadMessageInfo(v, e.user_id);
    });
  }
  window.updateReadMessageInfo = function (messageId, userId) {
    if (selectedContactId != userId) {
      return false;
    }
    $.each(conversationMessages, function (index, messageInfo) {
      if (messageInfo.id == messageId) {
        messageInfo.status = 1;
        messageInfo.updated_at = moment.utc().format('YYYY-MM-DD hh:mm:ss');
        var messageReadAtEle = $("#msg-read-at-" + messageId);
        if (messageReadAtEle.length > 0) {
          messageReadAtEle.text(getCalenderFormatForLastSeen(messageInfo.updated_at));
        }
        return false;
      }
    });
  };
  function blockUnblockUserEvent(e) {
    var currentUserId = $('.chat__person-box--active').data('id');
    if (loggedInUserId != e.blockedTo.id) {
      return;
    }
    if (!e.isBlocked && currentUserId == e.blockedBy.id) {
      $('.typing').show();
      $('#user-' + currentUserId).find('.chat__person-box-status').show();
      $('.chat-profile__person-status').show();
      $('.chat__area-wrapper').append(chatSendArea);
      $('.hdn-text-message').remove();
      $('.blocked-message-text').remove();
      loadEojiArea();
      sendMessage();
      removeValueFromArray(blockedUsersList, currentUserId);
    } else if (e.isBlocked) {
      if (currentUserId == e.blockedBy.id) {
        $('.chat__area-text').remove();
        $('.blocked-message-text').remove();
        $('.typing').hide();
        $('.chat__area-wrapper').append(blockedByMessageText);
      }
      $('#user-' + currentUserId).find('.chat__person-box-status').hide();
      $('.chat-profile__person-status').hide();
      blockedUsersList.push(e.blockedBy.id);
    }
  }
  function newMessageArrived(e) {
    var isArchiveChat = archivePeopleBodyEle.find('#user-' + e.from_id).length;
    if ($.inArray(selectedContactId, [e.from_id, e.to_id]) >= 0) {
      callAfterAPI = false;
      moveConversationFromArchiveToActiveChat();
      //already chat window is open whoes message has arrive
      setSentOrReceivedMessage(e);
      var fromUser = chatPeopleBodyEle.find('#user-' + e.from_id);
      var toUser = chatPeopleBodyEle.find('#user-' + e.to_id);
      if (fromUser.length) {
        addUserToTopOfConversation(e.from_id, fromUser);
      } else if (toUser.length) {
        addUserToTopOfConversation(e.to_id, toUser);
      }
      moveConversationFromArchiveToActiveChat();
    } else if (chatPeopleBodyEle.find('#user-' + e.from_id).length) {
      //chat window is not open so update message count
      var userEle = chatPeopleBodyEle.find('#user-' + e.from_id);
      var oldMsgCount = userEle.find('.chat__person-box-count').text();
      oldMsgCount = isNaN(oldMsgCount) || oldMsgCount === '' ? 0 : oldMsgCount;
      if (oldMsgCount == 0) {
        totalUnreadConversations += 1;
        updateUnreadMessageCount(0);
      }
      var newMsgCount = add(oldMsgCount, 1);
      userEle.find('.chat__person-box-count').removeClass('d-none');
      userEle.find('.chat__person-box-count').text(newMsgCount).show();
      userEle.find('.chat-message').html(getMessageByItsTypeForChatList(e.message, e.message_type, e.file_name));
      userEle.find('.chat__person-box-time').text(getLocalDate(e.created_at));
      addUserToTopOfConversation(e.from_id, userEle);
    } else {
      totalUnreadConversations += 1;
      updateUnreadMessageCount(0);
      //user not exist in chat-list so start new conversation
      var newUserEle = prepareNewConversation(e.from_id, htmlSpecialCharsDecode(e.sender.name), e, e.sender.photo_url);
      chatPeopleBodyEle.prepend(newUserEle);
      var _userEle = chatPeopleBodyEle.find('#user-' + e.from_id);
      _userEle.find('.chat__person-box-status').removeClass('chat__person-box-status--offline').addClass('chat__person-box-status--online');
      noConversationEle.hide();
      noConversationYetEle.hide();
      archivePeopleBodyEle.find('#user-' + e.from_id).remove();
    }
    if (isArchiveChat) {
      makeConversationArchiveWhenMessageArrive(e.from_id);
      makeActiveChatTabActive();
      showNoArchiveConversationEle();
    }
  }
  window.makeConversationArchiveWhenMessageArrive = function (userId) {
    $.ajax({
      type: 'get',
      url: route('conversations.archive-chat', userId),
      success: function success() {
        showNoArchiveConversationEle();
      }
    });
  };
  window.Echo["private"]("chat").listenForWhisper("start-typing.".concat(loggedInUserId), function (e) {
    if (latestSelectedUser == e.user.id) {
      var userTyping = e.user.name + ' Typing...';
      $('.typing').html(userTyping).show();
    }
  }).listenForWhisper("stop-typing.".concat(loggedInUserId), function (e) {
    if (latestSelectedUser == e.user.id) {
      $('.typing').html('online').show();
    }
  });
  function listenForGroupMessageTyping(groupId) {
    window.Echo["private"]("group.".concat(groupId)).listenForWhisper("group-message-typing", function (e) {
      var userTyping = e.data.name + ' Typing...';
      var currentGroupId = $('.chat__person-box--active').data('id');
      if (currentGroupId != e.data.groupId) {
        return;
      }
      $('.typing').removeClass('d-none');
      $('.typing').html(userTyping).show();
    }).listenForWhisper("stop-group-message-typing", function (e) {
      var currentGroupId = $('.chat__person-box--active').data('id');
      if (currentGroupId != e.data.groupId) {
        return;
      }
      $('.typing').hide();
    });
  }
  Echo.join("user-status").here(function (users) {
    setTimeout(function () {
      $.each(users, function (index, user) {
        updateUserStatus(user, 1);
      });
    }, 1000);
  }).joining(function (user) {
    updateUserStatus(user, 1);
  }).leaving(function (user) {
    updateUserStatus(user, 0);
  });
  window.prepareChatHeader = function (user, conversations) {
    var lastSeenTime = user.last_seen !== null && user.last_seen != '' ? getCalenderFormatForLastSeen(user.last_seen) : 'Never';
    /** If user is blocked then do not show last seen */
    var isGroup = $('.chat__person-box--active').data('is_group');
    var template = $.templates('#tmplConversation');
    var $htmlOutput = $(template.render({
      user: user,
      my_role: user.my_role ? user.my_role : 1,
      lastSeenTime: lastSeenTime,
      isGroup: isGroup
    }, {
      checkUserStatus: checkUserStatus
    }));
    var messageConversation = $htmlOutput.find('.chat-conversation');
    messageConversation.html('');
    if (conversations.length !== null) {
      messageConversation.html(conversations.map(prepareChatConversation).join(''));
    }
    return $htmlOutput.html();
  };
  window.prepareChatConversation = function (data) {
    var needToRemoveOldTimeline = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
    if (data.message_type === 9) {
      var _timeLineEle = addTimeLineEle(data.created_at, needToRemoveOldTimeline);
      var _template = $.templates('#tmplMessageBadges');
      var _helpers = {
        getLocalDate: getLocalDate
      };
      return _timeLineEle + _template.render(data, _helpers);
    }
    if ($.inArray(needToRemoveOldTimeline, [true, false]) === -1) {
      needToRemoveOldTimeline = true;
    }
    var timeLineEle = addTimeLineEle(data.created_at, needToRemoveOldTimeline);
    var isReceiver = false;
    var className = data.from_id == loggedInUserId ? 'chat-conversation__sender' : !data.status ? 'chat-conversation__receiver unread' : 'chat-conversation__receiver';
    var readUnread = data.hasOwnProperty('is_group') && data.is_group === 0 && data.status == 1 || data.hasOwnProperty('read_by_all_count') && data.read_by_all_count === 0 ? 'chat-container__read-status--read' : 'chat-container__read-status--unread';
    if (className.includes('chat-conversation__receiver')) {
      isReceiver = true;
    }
    var allowToDelete = true;
    var deleteMsgForEveryone = true;
    if (data.time_from_now_in_min > messageDeleteTime) {
      allowToDelete = false;
    }
    if (data.time_from_now_in_min > deleteMsgForEveryone) {
      deleteMsgForEveryone = false;
    }
    var templateData = {};
    var helpers = {
      displayMessage: displayMessage,
      getChatMagTimeInConversation: getChatMagTimeInConversation
    };
    var template = $.templates('#tmplMessage');
    templateData.data = data;
    templateData.isReceiver = isReceiver;
    templateData.loggedInUserId = loggedInUserId;
    templateData.checkShowNameChat = checkShowNameChat;
    templateData.authImage = $.parseHTML(authImgURL)[0].data;
    templateData.authUserName = authUserName;
    templateData.needToRemoveOldTimeline = needToRemoveOldTimeline;
    templateData.className = className;
    templateData.readUnread = readUnread;
    templateData.allowToDelete = allowToDelete;
    templateData.deleteMsgForEveryone = deleteMsgForEveryone;
    return timeLineEle + template.render(templateData, helpers);
  };
  window.displayMessage = function (data) {
    if (data.message_type === mediaTypeImage) {
      return imageRenderer(data.message);
    } else if (data.message_type === mediaTypePdf) {
      return fileRenderer(data.message, data.file_name, pdfURL);
    } else if (data.message_type === mediaTypeDocx) {
      return fileRenderer(data.message, data.file_name, docsURL);
    } else if (data.message_type === mediaTypeVideo) {
      return videoRenderer(data.message);
    } else if (data.message_type === youtubeUrl) {
      return renderYoutubeURL(data.message);
    } else if (data.message_type === mediaTypeTxt) {
      return fileRenderer(data.message, data.file_name, textURL);
    } else if (data.message_type === mediaTypeXls) {
      return fileRenderer(data.message, data.file_name, xlsURL);
    } else if (data.message_type === mediaTypeVoice) {
      return voiceRenderer(data.message, data.file_name);
    } else if (data.message_type === mediaTypeZip) {
      return fileRenderer(data.message, data.file_name, zipURL);
    } else {
      if (checkYoutubeUrl(data.message) === youtubeUrl) {
        return renderMultipleYouTubeUrl(data);
      }
      if (data.url_details) {
        var records = {
          urlDetails: data.url_details,
          message: data.message
        };
        return $.templates('#tmplLinkPreview').render(records);
      }
      return emojione.shortnameToImage(detectUrlFromTextMessage(data.message));
    }
  };
  window.checkYoutubeUrl = function (message) {
    var youtubeLink = 'youtube.com/watch?v=';
    if (message.indexOf(youtubeLink) != -1) {
      return youtubeUrl;
    }
    return 0;
  };
  window.findUrls = function (text) {
    var source = (text || '').toString();
    var urlArray = [];
    var matchArray;

    // Regular expression to find FTP, HTTP(S) and email URLs.
    var regexToken = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;

    // Iterate through any URLs in the text.
    while ((matchArray = regexToken.exec(source)) !== null) {
      var token = matchArray[0];
      urlArray.push(token);
    }
    return urlArray;
  };
  window.renderMultipleYouTubeUrl = function (data) {
    var messageClassName = data.from_id != loggedInUserId ? 'float-right' : 'float-left';
    var rendererClassName = data.from_id != loggedInUserId ? 'mr-2' : 'float-right ml-2';
    var urls = findUrls(data.message);
    var message = '';
    $.each(urls, function (index, url) {
      message += renderYoutubeURL(url, rendererClassName);
    });
    return message + '<div class="d-inline-block ' + messageClassName + ' mx-1" style="max-width: 500px;">' + data.message + '</div>';
  };
  window.addTimeLineEle = function (created_at) {
    var needToRemoveOldTimeline = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
    var timelineDate = getCalenderFormatForTimeLine(created_at);
    var timelineDateClass = timelineDate.split(' ').join('_').replace(',', '');
    var timeLineEle = '';
    var timeLineEleContent = '<div class="chat__msg-day-divider d-flex justify-content-center ' + timelineDateClass + '">\n' + '               <span class="chat__msg-day-title">' + timelineDate + '</span>\n' + '          </div>';
    if (timelineDate == 'Today' && $('.chat-conversation').find($('.chat__msg-day-title')).text() == 'Today') {
      return '';
    }
    if ($.inArray(timelineDate, previousDate) === -1) {
      //only new timeline will be added
      timeLineEle = timeLineEleContent;
      previousDate.push(timelineDate);
    } else if (needToRemoveOldTimeline) {
      //new timeline will be added and old will be REMOVED
      var oldTimeLineEle = $('.chat-conversation').find('.' + timelineDateClass);
      if (oldTimeLineEle.length) {
        $('.' + timelineDateClass).remove();
        timeLineEle = timeLineEleContent;
      }
    }
    return timeLineEle;
  };
  window.isTyping = function () {
    var channel = Echo["private"]("chat");
    var isGroup = $('.chat__person-box--active').data('is_group');
    if (isGroup) {
      groupChatTyping();
      return;
    }
    if (startTyping) {
      //fire start typing event
      channel.whisper("start-typing.".concat(latestSelectedUser), {
        user: {
          id: loggedInUserId,
          name: authUserName
        },
        typing: true
      });
    }
    startTyping = false;
    clearTimeout(timer);
    timer = setTimeout(stopTyping, 1000);
  };
  window.groupChatTyping = function () {
    var groupId = $('.chat__person-box--active').data('id');
    var channel = Echo["private"]("group.".concat(groupId));

    //fire start typing event
    channel.whisper("group-message-typing", {
      data: {
        groupId: groupId,
        name: authUserName
      },
      typing: true
    });
    setTimeout(stopGroupChatTyping, 1000);
  };
  window.stopGroupChatTyping = function () {
    var groupId = $('.chat__person-box--active').data('id');
    var channel = Echo["private"]("group.".concat(groupId));
    //fire stop typing event
    channel.whisper("stop-group-message-typing", {
      data: {
        groupId: groupId,
        name: authUserName
      },
      typing: true
    });
  };
  window.stopTyping = function () {
    startTyping = true;
    var channel = Echo["private"]("chat");

    //fire stop typing event
    channel.whisper("stop-typing.".concat(latestSelectedUser), {
      user: {
        id: loggedInUserId
      },
      typing: true
    });
  };
  window.setNoConversationYet = function () {
    var conversationListLength = $('#chatPeopleBody').find('.chat__person-box').length;
    var archiveConversationListLength = $('#archivePeopleBody').find('.chat__person-box').length;
    noConversationYet = conversationListLength > 0 ? false : true;
    noArchiveConversationYet = archiveConversationListLength > 0 ? false : true;
    if (!noConversationYet) {
      noConversationYetEle.hide();
    } else {
      noConversationYetEle.show();
    }
    if (!noArchiveConversationYet) {
      noArchiveConversationYetEle.hide();
    } else {
      noArchiveConversationYetEle.show();
    }
  };

  // user search event
  $(document).on('click', '.user-list-chat-select__list-item', function () {
    $('.user-list-chat-select .user-list-chat-select__list-item').removeClass('user-list-chat-select__list-item--active');
    $(this).addClass('user-list-chat-select__list-item--active');
    startNewConversation();
  });
  window.startNewConversation = function () {
    var selectedUserId = $('.user-list-chat-select__list-item--active input')[0].value;
    var isMyContact = checkForMyContact(selectedUserId);
    var selectedUserProfilePicUrl = $('.user-list-chat-select__list-item--active img').attr('src');
    var selectedUserName = $('.user-list-chat-select__list-item--active .add-user-contact-name').text();
    $('#addNewChat').modal('toggle');
    var isUserElePresent = $('.chat__people-body').find('#user-' + selectedUserId).length;
    var isUserElePresentInArchiveChat = archivePeopleBodyEle.find('#user-' + selectedUserId).length;
    if (isUserElePresentInArchiveChat) {
      makeArchiveChatTabActive();
    } else {
      makeActiveChatTabActive();
    }
    var selectedUserStatus = $('.user-' + selectedUserId).data('status');
    var isGroup = $('.user-' + selectedUserId).data('is-group');
    if (!isUserElePresent) {
      var newUserEle = prepareNewConversation(selectedUserId, selectedUserName, '', selectedUserProfilePicUrl, selectedUserStatus, isGroup ? 1 : 0);
      chatPeopleBodyEle.prepend(newUserEle);
      if (!isGroup) {
        newConversationStartedUserIds.push(selectedUserId);
      }
    }
    $('#user-' + selectedUserId).trigger('click');
    $('#user-' + selectedUserId).attr('data-is_my_contact', isMyContact);
    setNoConversationYet();
  };
  window.checkForMyContact = function (selectedContactId) {
    var inMyContacts = $.inArray(selectedContactId.toString(), myContactIds);
    return inMyContacts > -1 ? 1 : 0;
  };
  window.prepareNewConversation = function (userId, name) {
    var messageInfo = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
    var profilePic = arguments.length > 3 ? arguments[3] : undefined;
    var status = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : '';
    var isGroup = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;
    var count = 0;
    if (messageInfo !== '') {
      status = messageInfo.sender.is_online;
      count = 1;
    }
    var template = $.templates('#tmplContact');
    var helpers = {
      getMessageByItsTypeForChatList: getMessageByItsTypeForChatList,
      getLocalDate: getLocalDate
    };
    return template.render({
      id: userId,
      name: name,
      photo_url: profilePic,
      status: status,
      isGroup: isGroup,
      messageInfo: messageInfo,
      count: count
    }, helpers);
  };
  window.makeUserOnlineOffline = function (ele, status) {
    if (status) {
      ele.find('.chat__person-box-status').removeClass('chat__person-box-status--offline').addClass('chat__person-box-status--online');
    } else {
      ele.find('.chat__person-box-status').removeClass('chat__person-box-status--online').addClass('chat__person-box-status--offline');
    }
  };

  // getting data on modal for search user
  $('#addNewChat').on('show.bs.modal', function () {
    $('.user-list-chat-select .user-list-chat-select__list-item').removeClass('user-list-chat-select__list-item--active');
    $('#userListForChat').children('li').removeAttr('style');
  });
  $('#addNewChat').on('hidden.bs.modal', function () {
    clearSearchAndFilterOfMyContactsTab();
    clearSearchAndFilterOfNewContactsTab();
    clearSearchOfGroupsTab();
    clearSearchOfBlockedContactsTab();
  });
  window.clearSearchAndFilterOfMyContactsTab = function () {
    var myContactsFilter = $('input[type="checkbox"][name="my_contacts_filter"]');
    var searchMyContactForChat = $('#searchMyContactForChat').val();
    if (myContactsFilter.prop("checked") == true || searchMyContactForChat != '') {
      myContactsFilter.prop("checked", false);
      window.livewire.emit('clearSearchMyContacts');
    }
    $('#searchMyContactForChat').val('');
  };
  window.clearSearchAndFilterOfNewContactsTab = function () {
    var searchContactForChat = $('#searchContactForChat').val();
    var newContactsFilter = $('input[type="checkbox"][name="new_contact_gender"]');
    if (newContactsFilter.prop("checked") == true || searchContactForChat != '') {
      newContactsFilter.prop("checked", false);
      window.livewire.emit('clearSearchUsers');
    }
    $('#searchContactForChat').val('');
  };
  window.clearSearchOfGroupsTab = function () {
    var searchGroup = $('#searchGroupsForChat').val();
    if (searchGroup != '') {
      window.livewire.emit('clearSearchGroup');
    }
    $('#searchGroupsForChat').val('');
  };
  window.clearSearchOfBlockedContactsTab = function () {
    var searchBlockUsers = $('#searchBlockUsers').val();
    if (searchBlockUsers != '') {
      window.livewire.emit('clearSearchOfBlockedUsers');
    }
    $('#searchBlockUsers').val('');
  };
  window.prepareContactForModal = function (users) {
    var addSingleUser = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    var helpers = {
      getGender: getGender,
      checkUserStatusForGroupMember: checkUserStatusForGroupMember
    };
    var template = $.templates('#tmplAddChatUsersList');
    var htmlOutput = template.render(users, helpers);
    if (addSingleUser) {
      $('#userListForChat').append(htmlOutput);
    } else {
      $('#userListForChat').html(htmlOutput);
    }
  };
  window.getGender = function (gender) {
    if (gender == 1) {
      return 'male';
    }
    if (gender == 2) {
      return 'female';
    }
    return '';
  };
  window.prepareBlockedUsers = function (users) {
    var addSingleUser = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    $('.no-blocked-user').hide();
    var template = $.templates('#tmplBlockedUsers');
    var htmlOutput = template.render(users);
    if (addSingleUser) {
      $('#blockedUsersList').append(htmlOutput);
    } else {
      $('#blockedUsersList').html(htmlOutput);
    }
  };
  window.searchEleSearchEvent = function (searchEle) {
    searchEle.on('search', function () {
      if (!this.value) {
        searchEle.trigger('keyup');
      }
    });
  };
  window.searchUsers = function () {
    var searchResult = [];
    var searchEle = $('#searchUserInput');
    searchEleSearchEvent(searchEle);
    searchEle.on('keyup', function () {
      searchResult = [];
      $('#loadMoreConversationBtn.active').hide();
      $('#loadMoreArchiverConversationBtn.active').hide();
      var value = $(this).val().toLowerCase();
      var activeNavTab = getActiveNavChatTabId();
      $('#' + activeNavTab + ' .chat__person-box').filter(function () {
        $(this).toggle($(this).find('.chat__person-box-name').text().toLowerCase().indexOf(value) > -1);
        searchResult.push($(this).find('.chat__person-box-name').text().toLowerCase().indexOf(value));
      });
      if (value == '') {
        $('#loadMoreConversationBtn.active').show();
        $('#loadMoreArchiverConversationBtn.active').show();
      }
      if (activeNavTab === 'chatPeopleBody') {
        ifUserNotPresentShowNoRecordFound(searchResult, 'no-conversation');
      }
      if (activeNavTab === 'archivePeopleBody') {
        ifUserNotPresentShowNoRecordFound(searchResult, 'no-archive-conversation');
      }
    });
  };
  $(document).on('click', '#activeChatTab', function () {
    resetSearch();
  });
  $(document).on('click', '#archiveChatTab', function () {
    resetSearch();
  });
  window.resetSearch = function () {
    var searchEle = $('#searchUserInput').val('');
    searchEle.trigger('keyup');
  };
  window.getActiveNavChatTabId = function () {
    return $(".chat__tab-content").find('.active').attr('id');
  };
  window.removeValueFromArray = function (arr, arrValue) {
    for (var i = 0; i < arr.length; i++) {
      if (arr[i] === arrValue) {
        arr.splice(i, 1);
      }
    }
    return arr;
  };
  window.ifUserNotPresentShowNoRecordFound = function (searchResult, noConversationClassName) {
    var isUserPresent = false;
    $.each(searchResult, function (index, value) {
      if (value >= 0) {
        isUserPresent = true;
        return false;
      }
    });
    if (isUserPresent) {
      $('.' + noConversationClassName).hide();
      return false;
    } else {
      var activeTab = getActiveNavChatTabId();
      var searchInput = $('#searchUserInput').val();
      if (searchInput !== '') {
        if (activeTab === 'chatPeopleBody') {
          noConversationYetEle.hide();
        }
        if (activeTab === 'archivePeopleBody') {
          noArchiveConversationYetEle.hide();
        }
        $('.' + noConversationClassName).show();
      } else {
        if (noConversationYet && activeTab === 'chatPeopleBody') {
          noConversationYetEle.show();
        }
        if (noArchiveConversationYet && activeTab === 'archivePeopleBody') {
          noArchiveConversationYetEle.show();
        }
        $('.' + noConversationClassName).hide();
      }
      return true;
    }
  };
  window.updateUserStatus = function (user, status) {
    //recent chat-list ele
    var UserEle = chatPeopleBodyEle.find('#user-' + user.id);
    //new conversation ele (in pop up)
    var newUserEle = $('.user-' + user.id);
    var newUserEleParent = $('.chat-user-' + user.id);

    /** Do not show user status when user is blocked */
    if ($.inArray(user.id, blockedUsersList) != -1) {
      return;
    }
    if (status == 1) {
      UserEle.find('.chat__person-box-status').removeClass('chat__person-box-status--offline').addClass('chat__person-box-status--online');

      //conversation
      if ($('#toId').val() == user.id) {
        $('.typing').html('online');

        //user profile
        $('.chat-profile__person-status').show().text('online');
        $('.chat-profile__person-last-seen').hide();
      }
      newUserEle.find('.chat__person-box-status').removeClass('chat__person-box-status--offline').addClass('chat__person-box-status--online');
      newUserEle.attr('data-status', 1);
      newUserEleParent.removeClass('online').removeClass('offline').addClass('online');
    } else {
      UserEle.find('.chat__person-box-status').removeClass('chat__person-box-status--online').addClass('chat__person-box-status--offline');
      newUserEle.find('.chat__person-box-status').removeClass('chat__person-box-status--online').addClass('chat__person-box-status--offline');
      newUserEle.attr('data-status', 0);
      newUserEleParent.removeClass('online').removeClass('offline').addClass('offline');
      var last_seen = 'last seen at: ' + getCalenderFormatForLastSeen(Date(), 'hh:mma', 0);
      $('.typing').html(last_seen);
      var isGroup = $('.chat__person-box--active').data('is_group');
      if (isGroup) {
        return;
      }
      //user profile
      $('.chat-profile__person-last-seen').show().text(last_seen);
      $('.chat-profile__person-status').hide();
    }
  };
  window.getMessageByScroll = function (isGroup) {
    // let isGroup = $('.chat__person-box--active').data('is_group');
    $('.chat-conversation').on('scroll', function () {
      if ($(this).scrollTop() === 0) {
        shouldCallApiTop = callBeforeAPI ? true : false;
        if (shouldCallApiTop === true) {
          var _isGroup = $('.chat__person-box--active').data('is_group');
          var reqData = {
            'before': lastMessageIdForScroll,
            'is_group': _isGroup
            // 'limit': limit,
          };

          getOldOrNewConversation(reqData, 1, 0);
        }
      } else if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 1) {
        var unreadIds = [];
        $('.chat-conversation .unread').each(function () {
          unreadIds.push($(this).data('message_id'));
        });
        if (unreadIds.length > 0) {
          fireReadMessageEventUsingIds(unreadIds);
        }
        var messageCount = $('.chat__person-box--active').find('.chat__person-box-count').text();
        messageCount = isNaN(messageCount) || messageCount === '' ? 0 : messageCount;
        if (messageCount > 0) {
          // callAfterAPI = false, means do not load after messages when read time messages are incoming
          shouldCallApiBottom = callAfterAPI ? true : false;
        }
        if (shouldCallApiBottom === true) {
          var lastMessageIdForScrollBottom = $('.chat-conversation').children().last().attr('data-message_id');
          var anyNewMessages = $('.message-' + lastMessageIdForScrollBottom).next();
          if (anyNewMessages.length > 0) {
            return;
          }
          var _isGroup2 = $('.chat__person-box--active').data('is_group');
          var _reqData = {
            'after': lastMessageIdForScrollBottom,
            'is_group': _isGroup2
            // 'limit': limit,
          };

          getOldOrNewConversation(_reqData, 0, 1);
        }
      }
    });
  };
  window.getOldOrNewConversation = function (reqData, isBefore, isAfter) {
    var isGroup = $('.chat__person-box--active').data('is_group');
    $('.loading-message').removeClass('d-none');
    // let urlDetail = userURL + selectedContactId + '/conversation';
    $.ajax({
      type: 'GET',
      url: route('users.conversation', selectedContactId),
      data: reqData,
      success: function success(data) {
        var userOrGroupObj = isGroup ? data.data.group : data.data.user;
        if (data.success && latestSelectedUser === userOrGroupObj.id) {
          var conversations = data.data.conversations;
          $.merge(conversationMessages, conversations);
          if (conversations.length > 0) {
            if (isBefore) {
              var lastMsg = data.data.conversations[data.data.conversations.length - 1];
              lastMessageIdForScroll = lastMsg.id;
              $.each(conversations, function (index, conversation) {
                $('.chat-conversation').prepend(prepareChatConversation(conversation));
              });
              var scrolledAtEle = $('.message-' + reqData.before);
              scrollAtEle(scrolledAtEle);
              setOpenMsgInfoEvent();
            }
            if (isAfter) {
              $.each(conversations, function (index, conversation) {
                $('.chat-conversation').append(prepareChatConversation(conversation, false));
              });
              setOpenMsgInfoEvent();
              fireReadMessageEvent(conversations);
            }
          } else {
            if (isBefore) {
              shouldCallApiTop = false;
            }
            if (isAfter) {
              shouldCallApiBottom = false;
            }
          }
          if (scrollAtLastMsg && isAfter) {
            scrollToLastMessage();
          }
          $('.loading-message').addClass('d-none');
        }
      },
      error: function error(_error9) {
        console.log(_error9);
      }
    });
  };
  window.imageRenderer = function (message) {
    return "<a href=\"".concat(message, "\" data-ybox-group=\"group2\" class=\"yBox\"> <img src=\"").concat(message, "\"></a>");

    // return `<a href="${message}" data-fancybox="gallery" data-toggle="lightbox" data-gallery="example-gallery" data-src="${message}"><img src="${message}"></a>`
  };

  window.pdfRenderer = function (message, fileName) {
    return "<div class=\"media-wrapper d-flex align-items-center\"><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i><a href= \"".concat(message, "\"  target=\"blank\" class=\"item\"> ").concat(fileName, "</a></div>");
  };
  window.voiceRenderer = function (message, fileName) {
    return "<div class=\"media-wrapper d-flex align-items-center p-0\"><audio controls><source src=\"".concat(message, "\" type=\"audio/mp3\">\n            Your browser does not support the audio element.\n        </audio></div>");
  };
  window.docRenderer = function (message, fileName) {
    return "<div class=\"media-wrapper d-flex align-items-center\"><i class=\"fa fa-file-word-o\" aria-hidden=\"true\"></i><a href=\"".concat(message, "\"\n    target=\"_blank\">").concat(fileName, "</a></div>");
  };
  window.txtRenderer = function (message, fileName) {
    return "<div class=\"media-wrapper d-flex align-items-center\"><i class=\"fa fa-file-text-o\" aria-hidden=\"true\"></i><a href=\"".concat(message, "\"\n    target=\"_blank\">").concat(fileName, "</a></div>");
  };
  window.xlsRenderer = function (message, fileName) {
    return "<div class=\"media-wrapper d-flex align-items-center\"><img class=\"chat-file-preview\" src=\"".concat(xlsURL, "\" /><a href=\"").concat(message, "\"\n    target=\"_blank\">").concat(fileName, "</a></div>");
  };
  window.fileRenderer = function (message, fileName, fileIcon) {
    if (fileIcon) {
      return "<div class=\"media-wrapper d-flex align-items-center\"><img class=\"chat-file-preview\" src=\"".concat(fileIcon, "\" /><a href= \"").concat(message, "\"  target=\"blank\" class=\"item\"> ").concat(fileName, "</a></div>");
    }
    return "<div class=\"media-wrapper d-flex align-items-center\"><i class=\"fa ".concat(fileIcon, "\" aria-hidden=\"true\"></i><a href= \"").concat(message, "\"  target=\"blank\" class=\"item\"> ").concat(fileName, "</a></div>");
  };
  window.videoRenderer = function (message) {
    return "<div class=\"chat-media\">\n                     <video id=\"my-video\" class=\"video-js\" controls preload=\"auto\" width=\"640\" height=\"264\" data-setup=''>\n                            <source src=\"".concat(message, "\" type=\"video/mp4\">\n                            <source src=\"").concat(message, "\" type=\"video/webm\">\n                            <p class=\"vjs-no-js\">\n                                To view this video please enable JavaScript, and consider upgrading to a web browser that\n                                <a href=\"https://videojs.com/html5-video-support/\" target=\"_blank\">supports HTML5 video</a>\n                            </p>\n                      </video>\n                </div>");
  };
  window.renderYoutubeURL = function (url) {
    var redererClassName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    var newUrl = getYoutubeEmbedURL(url);
    return "<iframe width=\"246\" height=\"246\" style=\"border-radius:8px;\" class=\"" + redererClassName + "\" src=\"".concat(newUrl, "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen>\n            </iframe>");
  };
  window.getYoutubeEmbedURL = function (url) {
    var newUrl = url;
    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    var match = url.match(regExp);
    if (match && match[2].length === 11) {
      newUrl = 'https://www.youtube.com/embed/' + match[2] + '';
    }
    return newUrl;
  };
  window.format = function (dateTime) {
    var format = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'DD-MMM-YYYY';
    return moment(dateTime).locale(currentLocale).format(format);
  };
  window.imageRendererInSideMedia = function (message, id) {
    return "<a href=\"".concat(message, "\" data-ybox-group=\"group2\" class=\"yBox\" id=\"mediaProfile-").concat(id, "\"><img src=\"").concat(message, "\"></a>");
    // return `<a href="${message}" data-fancybox="gallery" data-bs-toggle="media" data-gallery="media-gallery" data-src="${message}" id="mediaProfile-${id}"><img src="${message}"></a>`
  };

  window.sideMediaRenderer = function (message, fileName, fileIcon, id) {
    return "<div class=\"media-wrapper d-flex align-items-center profile-media\" id=\"mediaProfile-".concat(id, "\" title=\"").concat(fileName, "\" ><a href= \"").concat(message, "\" target=\"blank\" class=\"item\"> <i class=\"fa ").concat(fileIcon, "\" aria-hidden=\"true\" ></i></a></div>");
  };
  window.getLocalDate = function (dateTime) {
    var format = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'hh:mma';
    if (isUTCTimezone == '0') {
      return moment(dateTime).format(format);
    }

    // let date = moment(dateTime).utc(dateTime).local();
    var date = moment(dateTime).local();
    return date.calendar(null, {
      sameDay: format,
      lastDay: '[Yesterday]',
      lastWeek: 'M/D/YY',
      sameElse: 'M/D/YY'
    });
  };
  window.getChatMagTimeInConversation = function (dateTime) {
    var format = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'h:mma';
    if (isUTCTimezone == '0') {
      return moment(dateTime).format(format);
    }
    return moment.utc(dateTime).local().format(format);
  };
  window.getCalenderFormatForLastSeen = function (dateTime) {
    var format = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'hh:mma';
    var needToConvertLocalDate = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1;
    // let date = (needToConvertLocalDate) ? moment(dateTime).utc(dateTime).local() : moment(dateTime);
    var date = needToConvertLocalDate ? moment(dateTime).local() : moment(dateTime);
    return date.calendar(null, {
      sameDay: '[Today], ' + format,
      lastDay: '[Yesterday], ' + format,
      lastWeek: 'dddd, ' + format,
      sameElse: function sameElse() {
        if (moment().year() === moment(dateTime).year()) {
          return 'MMM D, ' + format;
        } else {
          return 'MMM D YYYY, ' + format;
        }
      }
    });
  };
  window.getCalenderFormatForTimeLine = function (dateTime) {
    return moment(dateTime).utc(dateTime).local().calendar(null, {
      sameDay: '[Today]',
      lastDay: '[Yesterday]',
      lastWeek: 'dddd, MMM Do',
      sameElse: function sameElse() {
        if (moment().year() === moment(dateTime).year()) {
          return 'dddd, MMM Do';
        } else {
          return 'dddd, MMM Do YYYY';
        }
      }
    });
  };
  window.addNoMessagesIndicator = function () {
    var noMsgTemplate = $.templates('#tmplNoMessagesYet');
    var htmlOutput = noMsgTemplate.render();
    $('#conversation-container').html(htmlOutput);
  };
  window.detectUrlFromTextMessage = function (message) {
    var regex = /((http|https|ftp):\/\/[a-zа-я0-9\w?=&.\/-;#~%-]+(?![a-zа-я0-9\w\s?&.\/;#~%"=-]*>))/g;
    // Replace plain text links by hyperlinks
    return message.replace(regex, '<a href=\'$1\' target=\'_blank\'>$1</a>');
  };
  window.addNoConversationIndicator = function () {
    var noConversationYet = $.templates('#tmplConversationYet');
    var htmlOutput = noConversationYet.render();
    $('.chat__area-wrapper').html(htmlOutput);
  };
  window.fireSwal = function () {
    var icon = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'success';
    var title = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'Deleted!';
    var text = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'Message has been deleted!';
    var confirmButtonColor = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '#20a8d8';
    var timer = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 2000;
    Swal.fire({
      icon: icon,
      title: title,
      text: text,
      confirmButtonColor: '#20a8d8',
      timer: 2000
    });
  };
  window.deleteConversation = function (userId) {
    $.ajax({
      type: 'get',
      url: route('conversations.destroy', userId),
      success: function success(data) {
        if (data.success !== true) {
          return false;
        }
        fireSwal('success', 'Deleted!', 'Conversation has been deleted!');
        var triggerEventRemaining = false;
        if (latestSelectedUser === userId) {
          var selectedUserEle = $('#user-' + latestSelectedUser).parents('.contact-area');
          if (selectedUserEle.length === 0) {
            selectedUserEle = $('#user-' + latestSelectedUser);
          }
          var nextEle = selectedUserEle.nextAll('.contact-area:first').find('.chat__person-box');
          if (nextEle.length === 0) {
            nextEle = selectedUserEle.nextAll('.chat__person-box:first');
          }
          if (nextEle.length > 0) {
            $('#user-' + nextEle.data('id')).trigger('click');
          } else {
            var prevEle = selectedUserEle.prevAll('.contact-area:first').find('.chat__person-box');
            if (prevEle.length === 0) {
              prevEle = selectedUserEle.prevAll('.chat__person-box:first');
            }
            if (prevEle.length) {
              $('#user-' + prevEle.data('id')).trigger('click');
            }
            if (prevEle.length === 0) {
              triggerEventRemaining = true;
            }
          }
          if (nextEle.length === 0 && triggerEventRemaining) {
            triggerEventRemaining = true;
          }
        }
        var userEle = $('#user-' + userId).parents('.contact-area');
        if (userEle.length === 0) {
          userEle = $('#user-' + userId);
        }
        var activeChat = userEle.parents('#chatPeopleBody');
        var archiveChat = userEle.parents('#archivePeopleBody');
        userEle.remove();
        if (triggerEventRemaining) {
          var _nextEle = $('#chatPeopleBody').find('.chat__person-box:first');
          if (_nextEle.length > 0) {
            $('#user-' + _nextEle.data('id')).trigger('click');
          }
        }
        if (activeChat.length > 0 && chatPeopleBodyEle.find('.chat__person-box').length === 0) {
          setNoConversationYet();
          addNoConversationIndicator();
          selectedContactId = 0;
        }
        if (archiveChat.length > 0 && $('#archivePeopleBody').find('.chat__person-box').length === 0) {
          setNoConversationYet();
          addNoConversationIndicator();
          selectedContactId = 0;
        }
      }
    });
  };
  var swalDelete = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-danger mr-2 btn-lg',
      cancelButton: 'btn btn-secondary btn-lg'
    },
    buttonsStyling: false
  });
  $(document).on('click', '.chat__person-box-delete', function (e) {
    var chatDelEle = $(this);
    var userId = chatDelEle.parents('.chat__person-box').data('id');
    var contactName = $('#user-' + userId).find('.contact-name').text().toString().trim();
    swalDelete.fire({
      title: Lang.get('messages.placeholder.are_you_sure'),
      html: Lang.get('messages.placeholder.delete_chat_with') + '<b>' + contactName + '</b>?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: Lang.get('messages.placeholder.yes_delete')
    }).then(function (result) {
      if (result.value) {
        deleteConversation(userId);
      }
    });

    //here we write stopPropagation to stop ajax call of select chat after this delete call, if we not write this than select conversation call will happen
    e.stopPropagation();
  });
  $(document).on('click', '.chat__person-box-archive', function (e) {
    var chatArchiveEle = $(this);
    var userId = chatArchiveEle.parents('.chat__person-box').data('id');
    var isArchiveChat = $(this).parents('#archivePeopleBody').length;
    var contactName = $('#user-' + userId).find('.contact-name').text();
    var ArchiveUnarchive = isArchiveChat ? 'Unarchive' : 'Archive';
    swalDelete.fire({
      title: 'Are you sure?',
      html: ArchiveUnarchive + ' chat with <b>' + contactName + '</b>?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes'
    }).then(function (result) {
      if (result.value) {
        if (ArchiveUnarchive == 'Unarchive') {
          unaArchiveConversation(userId);
        } else {
          archiveConversation(userId);
        }
      }
    });

    //here we write stopPropagation to stop ajax call of select chat after this delete call, if we not write this than select conversation call will happen
    e.stopPropagation();
  });
  window.archiveConversation = function (userId) {
    $.ajax({
      type: 'get',
      url: route('conversations.archive-chat', userId),
      success: function success(data) {
        if (data.success == true) {
          fireSwal('success', 'Archived!', 'Conversation has been archived!');
          var _archiveConversation = $('#user-' + userId);
          $('#user-' + userId).remove();
          _archiveConversation.find('.chat__person-box-archive').text('Unarchive Chat');
          if ($('#loadMoreArchiverConversationBtn.active').length > 0) {
            $('#loadMoreArchiverConversationBtn.active').before(_archiveConversation);
          } else {
            archivePeopleBodyEle.append(_archiveConversation);
          }
          makeArchiveChatTabActive();
          setNoConversationYet();
        }
      }
    });
  };
  window.unaArchiveConversation = function (userId) {
    $.ajax({
      type: 'get',
      url: route('conversations.un-archive-chat', userId),
      success: function success(data) {
        if (data.success == true) {
          fireSwal('success', 'Unarchived!', 'Conversation has been unarchived!');
          var _archiveConversation2 = $('#user-' + userId);
          $('#user-' + userId).remove();
          _archiveConversation2.find('.chat__person-box-archive').text('Archive Chat');
          if ($('#loadMoreConversationBtn.active').length > 0) {
            $('#loadMoreConversationBtn.active').before(_archiveConversation2);
          } else {
            chatPeopleBodyEle.append(_archiveConversation2);
          }
          makeActiveChatTabActive();
          setNoConversationYet();
        }
      }
    });
  };
  window.makeActiveChatTabActive = function () {
    $('.nav-item a[href="#chatPeopleBody"]').tab('show');
  };
  window.makeArchiveChatTabActive = function () {
    $('.nav-item a[href="#archivePeopleBody"]').tab('show');
  };
  window.showNoArchiveConversationEle = function () {
    if (archivePeopleBodyEle.find('.chat__person-box').length === 0) {
      noArchiveConversationEle.show();
    } else {
      noArchiveConversationEle.hide();
    }
  };
  window.showNoActiveConversationEle = function () {
    if (chatPeopleBodyEle.find('.chat__person-box').length === 0) {
      noConversationEle.show();
    } else {
      noConversationEle.hide();
    }
  };
  window.removeTimeline = function (messageEle) {
    var timeLineEle = messageEle.prev('.chat__msg-day-divider');
    if (timeLineEle.length) {
      var nextElementLength = messageEle.next().length;
      if (nextElementLength === 0) {
        timeLineEle.remove();
      }
    }
  };
  window.checkAllMsgAndShowNoMsgYet = function () {
    var conversationContainer = $('#conversation-container');
    var senderMsgLength = conversationContainer.find('.chat-conversation__sender').length;
    var receiverMsgLength = conversationContainer.find('.chat-conversation__receiver').length;
    var badgeMessages = $('.chat-conversation').find('#message-badges').length;
    if (senderMsgLength === 0 && receiverMsgLength === 0 && badgeMessages === 0) {
      var chatPersonBox = $('.chat__person-box--active');
      conversationContainer.html('');
      noMsgesYet.show();
      addNoMessagesIndicator();
      chatPersonBox.find('.chat-message').text('');
      chatPersonBox.find('.chat__person-box-time').text('');
    }
  };
  window.deleteMsgForEveryone = function (messageId, previousMessageId) {
    $.ajax({
      type: 'post',
      url: '/conversations/' + messageId + '/delete',
      data: {
        'previousMessageId': previousMessageId
      },
      success: function success(data) {
        if (data.success === true) {
          var previousMessage = data.data.previousMessage;
          fireSwal('success', 'Deleted!', 'Message has been deleted!');
          var messageEle = $('.message-' + messageId);
          removeTimeline(messageEle);

          /** UPDATE MEDIA IN PROFILE BAR*/
          removeMediaFromProfileBar(messageId);
          if (previousMessage != null && messageEle.nextAll('#send-receive-direction:first').length === 0) {
            var chatPersonBox = $('.chat__person-box--active');
            chatPersonBox.find('.chat-message').html(getMessageByItsTypeForChatList(previousMessage.message, previousMessage.message_type, previousMessage.file_name));
            chatPersonBox.find('.chat__person-box-time').text(getLocalDate(previousMessage.created_at));
          }
          messageEle.remove();
          checkAllMsgAndShowNoMsgYet();
        }
      },
      error: function error(result) {
        displayToastr('Error', 'error', result.responseJSON.message);
      }
    });
  };
  window.deleteMessage = function (messageId, previousMessageId) {
    $.ajax({
      type: 'post',
      url: route('conversations.message-conversation.delete', messageId),
      data: {
        'previousMessageId': previousMessageId
      },
      success: function success(data) {
        if (data.success === true) {
          var previousMessage = data.data.previousMessage;
          fireSwal('success', 'Deleted!', 'Message has been deleted!');
          var messageEle = $('.message-' + messageId);
          removeTimeline(messageEle);

          /** UPDATE MEDIA IN PROFILE BAR*/
          removeMediaFromProfileBar(messageId);
          if (previousMessage != null && messageEle.nextAll('#send-receive-direction:first').length === 0) {
            var chatPersonBox = $('.chat__person-box--active');
            chatPersonBox.find('.chat-message').html(getMessageByItsTypeForChatList(previousMessage.message, previousMessage.message_type, previousMessage.file_name));
            chatPersonBox.find('.chat__person-box-time').text(getLocalDate(previousMessage.created_at));
          }
          messageEle.remove();
          checkAllMsgAndShowNoMsgYet();
        }
      },
      error: function error(result) {
        displayToastr('Error', 'error', result.responseJSON.message);
      }
    });
  };

  /** User Profile Updated */
  Echo["private"]("updates").listen(".Modules\\Chat\\Events\\UpdatesEvent", function (e) {
    if (e.type == 1) {
      //user profile updates
      var user = e.user;
      $('#userListForChat').find('.chat-user-' + user.id).find('img').attr('src', user.photo_url);
      $('#myContactListForChat').find('.chat-user-' + user.id).find('.user-avatar-img').attr('src', user.photo_url);
      $('#myContactListForAddPeople').find('.chat-user-' + user.id).find('.user-avatar-img').attr('src', user.photo_url);
      $('#groupMembersList .new-group-members__list').find('.user-' + user.id).find('.user-avatar-img').attr('src', user.photo_url);
      $('.chat__area-header').find('.chat__area-header-avatar').find('img').attr('src', user.photo_url);
      $('#userListForChat').find('.chat-user-' + user.id).find('.add-user-contact-name').text(user.name);
      $('#user-' + user.id).find('img').attr('src', user.photo_url);
      $('#user-' + user.id).find('.contact-name').text(user.name);
      $('.user-chat-image-' + user.id).attr('src', user.photo_url).attr('data-original-title', user.name);
      var groupMember = $('#nav-group-members').find('#user-' + user.id);
      groupMember.find('.contact-name').text(user.name);
      groupMember.find('img').attr('src', user.photo_url);
    } else if (e.type == 2) {
      //set user status
      var userStatus = e.user_status;
      var userEle = $('#user-' + userStatus.user_id);
      var isMyContact = userEle.attr('data-is_my_contact');
      if (isMyContact == '0' || isMyContact == 'false') {
        return false;
      }
      var _groupMember = $('#nav-group-members').find('#user-' + userStatus.user_id).find('.group-user-status');
      var myContactUser = $('#myContactListForChat').find('.chat-user-' + userStatus.user_id).find('.my-contact-user-status');
      var template = $.templates('#tmplUserNewStatus');
      var htmlOutput = template.render(userStatus);
      userEle.find('.contact-status').html(htmlOutput);
      _groupMember.html(htmlOutput);
      myContactUser.html(htmlOutput);
      if (selectedContactId == userStatus.user_id) {
        $('.contact-title-status').html(htmlOutput);
      }
      loadTooltip();
    } else if (e.type == 3) {
      // clear user status
      $('#user-' + e.user_id).find('.contact-status').html('');
      if (selectedContactId == e.user_id) {
        $('.contact-title-status').html('');
      }
      $('#nav-group-members').find('#user-' + e.user_id).find('.group-user-status').html('');
      $('#myContactListForChat').find('.chat-user-' + e.user_id).find('.my-contact-user-status').html('');
      loadTooltip();
    }
  });
  function prepareNewGroupChatConversation(groupObj) {
    var msgText = groupObj.group_created_by + ' added you in group ' + '"' + groupObj.name + '"';
    var isGroupTabExist = $('#user-' + groupObj.id);
    if (isGroupTabExist.length > 0) {
      $('#user-' + groupObj.id).find('.chat-message').text(msgText);
      $('#user-' + groupObj.id).find('.chat__person-box-count').removeClass('d-none').text(1);
      $('#user-' + groupObj.id).find('.chat__person-box-time').text(getLocalDate(groupObj.created_at));
      noConversationEle.hide();
      listenForGroupUpdates(groupObj.id);
      return;
    }

    //group not exist in chat-list so start new conversation
    var newUserEle = prepareNewConversation(groupObj.id, htmlSpecialCharsDecode(groupObj.name), '', groupObj.photo_url, '', 1);
    chatPeopleBodyEle.prepend(newUserEle);
    $('#user-' + groupObj.id).find('.chat-message').text(msgText);
    $('#user-' + groupObj.id).find('.chat__person-box-count').removeClass('d-none').text(1);
    $('#user-' + groupObj.id).find('.chat__person-box-time').text(getLocalDate(groupObj.created_at));
    noConversationEle.hide();
    listenForGroupUpdates(groupObj.id);
  }
  window.listenForGroupUpdates = function (groupId) {
    /** Group Updated Event */
    Echo["private"]("group.".concat(groupId)).listen('.Modules\\Chat\\Events\\GroupEvent', function (e) {
      var group = e;
      var currentGroupId = $('.chat__person-box--active').data('id');

      // Group details updated
      if (e.type === 1) {
        updateGroupDetails(e.group);
      } else if (e.type === 2 && currentGroupId == e.group.id) {
        // Group member role changed
        updateGroupMemberRole(e.group.id, e.user_id, e.is_admin, e.userIds);
      } else if (e.type === 3 && currentGroupId == e.group.id) {
        // member removed from group
        removeMemberFromGroup(e.group.id, e.user_id);
      } else if (e.type === 4 && currentGroupId == e.group.id) {
        // new members added into group
        var _data2 = e.group;
        _data2.my_role = myRoleInGroup;
        _data2.users = e.group.users;
        addMembersToGroup(e.group.id, _data2);
      } else if (e.type === 5 && currentGroupId == e.group.id) {
        // group deleted by owner
        groupDeletedByOwner(e.group.id);
      } else if (e.type === 6 && currentGroupId == e.group.id) {
        // message read by all group members
        setTimeout(function () {
          $('.message-' + e.conversation_id).find('.chat-container__read-status').addClass('chat-container__read-status--read');
        }, 1000);
      } else if (e.type === 7) {
        // new group message arrived
        groupMessageArrived(e);
      } else if (e.type === 8) {
        // group messages read by group member
        updateReadByUsersOfGroupMessage(e);
      }
      if ($.inArray(e.type, [3, 4, 5, 1]) >= 0) {
        window.livewire.emit('searchGroup');
      }
    });
  };
  window.updateReadByUsersOfGroupMessage = function (readByInfo) {
    if (currentConversationGroupId !== readByInfo.group.id || readByInfo.conversation_ids.length === 0) {
      return false;
    }
    $.each(readByInfo.conversation_ids, function (index, conversationId) {
      var msgReadByUsersEle = $(document).find('.message-' + conversationId + '-read-by-users');
      $.each(conversationMessages, function (index, messageInfo) {
        if (messageInfo.id == conversationId) {
          $.each(messageInfo.read_by, function (index, val) {
            if (val.read_at == null && val.user_id == readByInfo.read_by_user_id) {
              val.read_at = readByInfo.read_at;
              return false;
            }
          });
          if (msgReadByUsersEle.length > 0) {
            showConversationInfo(messageInfo);
          }
          return false;
        }
      });
    });
    setReadByUsersCount();
  };
  function groupMessageArrived(e) {
    if (selectedContactId == e.to_id) {
      var groupEle = chatPeopleBodyEle.find('#user-' + e.to_id);
      var msgCountEle = groupEle.find('.chat__person-box-count');
      var msgCount = msgCountEle.text();
      msgCount = isNaN(msgCount) || msgCount === '' ? 0 : msgCount;
      callAfterAPI = false;

      // if (msgCount > 0) {
      //     updateMessageInSenderwindow(e);
      //     msgCount = add(msgCount, 1);
      //     $('.chat__person-box--active').
      //         find('.chat__person-box-count').
      //         text(msgCount).
      //         removeClass('d-none');
      //     return;
      // }

      //already chat window is open whoes message has arrive
      setSentOrReceivedMessage(e);
      if (groupEle.length) {
        addUserToTopOfConversation(e.to_id, groupEle);
      }

      // It will read upto 7 messages from 1st to 7th and other messages are managed by scroll
      // if ($('.chat-conversation').length <= 7) {
      //     fireReadMessageEventUsingIds([e.id]);
      // }
    } else if (chatPeopleBodyEle.find('#user-' + e.to_id).length) {
      //chat window is not open so update message count
      var _groupEle = chatPeopleBodyEle.find('#user-' + e.to_id);
      var _msgCountEle = _groupEle.find('.chat__person-box-count');
      var oldMsgCount = _msgCountEle.text();
      oldMsgCount = isNaN(oldMsgCount) || oldMsgCount === '' ? 0 : oldMsgCount;
      if (oldMsgCount == 0) {
        totalUnreadConversations += 1;
        updateUnreadMessageCount(0);
      }
      var newMsgCount = add(oldMsgCount, 1);
      _msgCountEle.removeClass('d-none').text(newMsgCount).show();
      _groupEle.find('.chat-message').html(getMessageByItsTypeForChatList(e.message, e.message_type, e.file_name));
      _groupEle.find('.chat__person-box-time').text(getLocalDate(e.created_at));
      addUserToTopOfConversation(e.to_id, _groupEle);
    } else {
      totalUnreadConversations += 1;
      updateUnreadMessageCount(0);
      //group not exist in chat-list so start new conversation
      var newUserEle = prepareNewConversation(e.to_id, htmlSpecialCharsDecode(e.group.name), e, e.group.photo_url, '', 1);
      chatPeopleBodyEle.prepend(newUserEle);
      noConversationEle.hide();
    }
  }
  function groupDeletedByOwner(groupId) {
    $('.members-count').hide();
    $('.div-group-members-nav').empty().append('<p class="no-group-members-found text-center">No group members found...</p>');
    $('.btn-add-members').parent().remove();
    $('.btn-leave-from-group').parent().remove();
    $('.chat__area-text').remove();
    $('.edit-group').parent().remove();
  }
  function prepareEditGroupIconHTML(groupId) {
    return '<div class="col-2"><i class="fa fa-edit edit-group pointer text-center" data-id="' + groupId + '"></i></div>';
  }
  function prepareButtonAddMembers(groupId) {
    return '<div class="chat-profile__column pb-0"><a href="#" class="btn btn-success btn-add-members" data-group-id="' + groupId + '">Add Members</a></div>';
  }
  function updateGroupDetails(group) {
    var currentGroupId = $('.chat__person-box--active').data('id');
    $('#groupDetailsDescription-' + group.id).text(group.description);
    if (!group.description) {
      $('#groupDetailsDescription-' + group.id).text('No description added yet...');
    }
    $('#groupDetailsName-' + group.id).text(group.name);
    $('#groupDetailsImage-' + group.id).attr('src', group.photo_url);
    $('#user-' + group.id).find('.chat__person-box-name').text(group.name);
    $('#user-' + group.id).find('.chat__person-box-avtar img').attr('src', group.photo_url);
    $('.group-list-' + group.id).find('img').attr('src', group.photo_url);
    $('.group-list-' + group.id).find('.add-user-contact-name').text(group.name);
    if (currentGroupId != group.id) {
      return;
    }
    $('.contact-title').text(group.name);
    $('.chat-header-img').attr('src', group.photo_url);
    if (group.group_type === 2 && group.my_role !== 2) {
      $('.chat__area-text').remove();
    } else {
      appendChatArea();
    }
    if (group.privacy === 2) {
      $('.group-profile-image').append(privateGroupIcon);
      if (myRoleInGroup === 1) {
        $('.btn-add-members').parent().remove();
      }
    } else {
      $('.group-profile-image').find('.private-group-badge').remove();
      if ($('.group-profile-data').find('.btn-add-members').length <= 0) {
        var addMemeberText = prepareButtonAddMembers(group.id);
        $('.btn-leave-from-group').parent().before(addMemeberText);
      }
    }
    if (group.group_type === 2) {
      $('.group-profile-image').append(closedGroupIcon);
      if (myRoleInGroup === 1) {
        $('.chat__area-text').remove();
      }
    } else {
      $('.group-profile-image').find('.closed-group-badge').remove();
      appendChatArea();
    }
  }
  function updateGroupMemberRole(groupId, memberId) {
    var isAdmin = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
    var members = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
    if (isAdmin) {
      var dismissAdminTag = prepareGroupAdminText(memberId, groupId, false);
      $('#makeAdmin-' + memberId).remove();
      $('.member-options-' + memberId).append(dismissAdminTag);
      $('.group-member-' + memberId).find('.chat__person-box-detail').append('<span class="badge badge-pill badge-success">Admin</span>');
      var editGroupIconTag = prepareEditGroupIconHTML(groupId);
      var editGroupIcon = $('.divGroupDetails').find('.edit-group');
      if (editGroupIcon.length === 0) {
        $('.divGroupDetails').find('.col-10').after(editGroupIconTag);
      }
      if (parseInt(loggedInUserId) === memberId) {
        addOptionsForAllMembers(members, groupId);
        $('.btn-add-members').show();
      }
    } else {
      var adminTag = prepareGroupAdminText(memberId, groupId);
      $('#dismissAdmin-' + memberId).remove();
      $('.member-options-' + memberId).append(adminTag);
      $('.group-member-' + memberId).find('.chat__person-box-detail span').remove();
      if (parseInt(loggedInUserId) === memberId) {
        $('.edit-group').parent().remove();
        removeOptionsForMember(members, groupId);
        $('.btn-add-members').hide();
      }
    }
  }
  function addOptionsForAllMembers(members, groupId) {
    $.each(members, function (index, value) {
      if (value === parseInt(loggedInUserId)) {
        return;
      }
      var template = $.templates('#tmplMemberOptions');
      var htmlOutput = template.render({
        member_id: value,
        group_id: groupId
      });
      $('.group-member-' + value).append(htmlOutput);
    });
  }
  function removeOptionsForMember(members, groupId) {
    $.each(members, function (index, value) {
      if (value === parseInt(loggedInUserId)) {
        return;
      }
      $('.group-member-' + value).find('.chat__person-box-msg-time').remove();
    });
  }
  function removeMemberFromGroup(groupId, userId) {
    membersCountArr[groupId] -= 1;
    $('.members-count').text(membersCountArr[groupId]);
    for (var i = 0; i < groupMembers.length; i++) {
      if (groupMembers[i] === parseInt(userId)) {
        groupMembers.splice(i, 1);
      }
    }
    $('.group-member-' + userId).remove();
    if (userId === parseInt(loggedInUserId)) {
      Echo.leave('group.' + groupId); // Unsubscribe to channel
      $('.chat__area-text').remove();
      var deleteButtonText = prepareDeleteGroupButton(groupId);
      $('.btn-add-members').parent().remove();
      $('.btn-leave-from-group').parent().remove();
      $('.group-profile-data').append(deleteButtonText);
    }
  }
  function addMembersToGroup(groupId, data) {
    membersCountArr[groupId] += data.users.length;
    $('.members-count').text(membersCountArr[groupId]);
    var template = $.templates('#tmplSingleGroupMember');
    var htmlOutput = template.render(data);
    $('.div-group-members-nav').append(htmlOutput);
  }
  $(document).on('click', '.msg-delete-for-everyone', function (e) {
    e.preventDefault();
    var messageDelEle = $(this);
    var messageId = messageDelEle.parent().parent().parent().parent().data('message_id');
    var messageEle = $('.message-' + messageId);
    var previousMessageEle = messageEle.prevAll('#send-receive-direction:first');
    var previousMessageId = 0;
    var badgeMsg = messageEle.prev('#message-badges');
    if (badgeMsg.length) {
      previousMessageId = badgeMsg.data('message_id');
    } else if (previousMessageEle.length) {
      previousMessageId = previousMessageEle.data('message_id');
    }
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'Delete this message?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!'
    }).then(function (result) {
      if (result.value) {
        deleteMsgForEveryone(messageId, previousMessageId);
      }
    });
    e.stopPropagation();
  });
  $(document).on('click', '.msg-delete-icon', function (e) {
    e.preventDefault();
    var messageDelEle = $(this);
    var messageId = messageDelEle.parent().parent().parent().parent().data('message_id');
    var messageEle = $('.message-' + messageId);
    var previousMessageEle = messageEle.prevAll('#send-receive-direction:first');
    var previousMessageId = 0;
    var badgeMsg = messageEle.prev('#message-badges');
    if (badgeMsg.length) {
      previousMessageId = badgeMsg.data('message_id');
    } else if (previousMessageEle.length) {
      previousMessageId = previousMessageEle.data('message_id');
    }
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'Delete this message?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!'
    }).then(function (result) {
      if (result.value) {
        deleteMessage(messageId, previousMessageId);
      }
    });
    e.stopPropagation();
  });
  function performActionsAfterUnblock(user) {
    $('.blocked-user-' + user.id).remove();
    prepareContactForModal({
      users: user
    }, true);
    if ($('#blockedUsersList').length <= 1) {
      $('.no-blocked-user').show();
      $('.no-blocked-user').find('span').text('No users blocked yet...');
    }
    $('.typing').show();
    $('#user-' + user.id).find('.chat__person-box-status').show();
    $('.chat-profile__person-status').show();
    $('#user-' + user.id).find('.contact-status').show();
    $('.contact-title-status').show();
    if ($('.chat__person-box--active').data('id') == user.id) {
      appendChatArea();
      $('.block-unblock-span').text('Block');
      $('.hdn-text-message').remove();
      $('.blocked-message-text').remove();
    }
  }
  function appendChatArea() {
    $('.chat__area-wrapper').append(chatSendArea);
    loadEojiArea();
    sendMessage();
  }
  function performActionAfterBlock(user) {
    $('.chat__area-text').remove();
    $('.blocked-message-text').remove();
    $('.block-unblock-span').text('Unblock');
    $('.chat__area-wrapper').append(blockedMessageText);
    $('.chat-user-' + user.id).remove();
    $('.typing').hide();
    $('#user-' + user.id).find('.chat__person-box-status').hide();
    $('.chat-profile__person-status').hide();
    $('#user-' + user.id).find('.contact-status').hide();
    $('.contact-title-status').hide();
    prepareBlockedUsers({
      users: user
    }, true);
  }
  function performActionAfterUnBlock(user) {
    appendChatArea();
    $('.blocked-message-text').remove();
    $('.block-unblock-span').text('Block');
    $('.typing').show().removeClass('d-none');
    $('#user-' + user.id).find('.chat__person-box-status').show();
    $('.chat-profile__person-status').show();
    $('#user-' + user.id).find('.contact-status').show();
    $('.contact-title-status').show();
  }
  function blockUnblockUser(data, blockedTo) {
    var isBlocked = data.is_blocked ? true : false;
    $.ajax({
      url: route('users.block-unblock', blockedTo),
      type: 'PUT',
      data: data,
      success: function success(result) {
        if (result.success) {
          displayToastr('Success', 'success', result.message);
          var user = result.data.user;
          if (isBlocked) {
            fireAddNewBlockedIdEvent(blockedTo);
            performActionAfterBlock(user);
          } else {
            fireRemoveBlockedIdEvent(blockedTo);
            performActionAfterUnBlock(user);
          }
        }
      },
      error: function error(result) {
        displayToastr('Error', 'error', result.responseJSON.message);
      }
    });
  }
  window.fireAddNewBlockedIdEvent = function (userId) {
    userId = parseInt(userId);
    blockedUsersList.push(userId);
    window.livewire.emit('addNewBlockedContactId', userId);
    window.livewire.emit('addBlockedUserId', userId);
  };
  window.fireRemoveBlockedIdEvent = function (userId) {
    userId = parseInt(userId);
    window.livewire.emit('removeBlockedContactId', userId);
    window.livewire.emit('removeBlockedUserId', userId);
    blockedUsersList = jQuery.grep(blockedUsersList, function (value) {
      return value != userId;
    });
  };

  /** UnBLock user */
  $(document).on('click', '.btn-unblock', function (e) {
    var _this = this;
    e.preventDefault();
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'Unblock this user?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes'
    }).then(function (result) {
      if (result.value) {
        placeCaret = false;
        var userId = $(_this).data('id');
        blockUnblockUser({
          is_blocked: false,
          blocked_to: userId
        }, userId);
      }
    });
  });

  /*** Block UnBLock */
  $(document).on('click', '.block-unblock-user-switch', function (e) {
    var isBlocked = $(this).is(':checked');
    var blockedTo = $('#senderId').val();
    var data = {};
    data.is_blocked = isBlocked;
    data.blocked_to = blockedTo;
    var blockUserText = isBlocked ? 'block' : 'unblock';
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'You want to ' + blockUserText + ' this user?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes'
    }).then(function (result) {
      if (!result.value) {
        $('.block-unblock-user-switch').prop('checked', isBlocked ? false : true);
        return;
      }
      blockUnblockUser(data, blockedTo);
    });
  });
  $(document).on('change', '#groupImage', function () {
    var ext = $(this).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
      $(this).val('');
      $('#groupValidationErrorsBox').html('The profile image must be a file of type: jpeg, jpg, png.').show();
    } else {
      displayPhoto(this, '#groupPhotoPreview');
    }
    setTimeout(function () {
      $('#groupValidationErrorsBox').hide();
    }, 3000);
  });
  $(document).on('change', '#editGroupImage', function () {
    var ext = $(this).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
      $(this).val('');
      $('#editGroupValidationErrorsBox').html('The profile image must be a file of type: jpeg, jpg, png.').show();
    } else {
      displayPhoto(this, '#editGroupPhotoPreview');
    }
    setTimeout(function () {
      $('#editGroupValidationErrorsBox').hide();
    }, 3000);
  });
  window.displayPhoto = function (input, selector) {
    var displayPreview = true;
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        var image = new Image();
        image.src = e.target.result;
        image.onload = function () {
          $(selector).attr('src', e.target.result);
          displayPreview = true;
        };
      };
      if (displayPreview) {
        reader.readAsDataURL(input.files[0]);
        $(selector).show();
      }
    }
  };

  /** Create Group OR Update Group */

  function createGroup(data) {
    $.ajax({
      type: 'post',
      url: route('groups.create'),
      data: data,
      processData: false,
      contentType: false,
      success: function success(result) {
        window.livewire.emit('newGroupCreated');
        window.livewire.emit('searchGroup');
        displayToastr('Success', 'success', result.message);
        prepareNewGroupChatConversation(result.data);
        $('#user-' + result.data.id).trigger('click');
        $('.chat__person-box--active').find('.chat__person-box-count').text(0).hide();
        $('#createNewGroup').modal('hide');
      },
      error: function error(result) {
        displayToastr('Error', 'error', result.responseJSON.message);
      },
      complete: function complete() {
        $('#btnCreateGroup').removeAttr('disabled');
        $('#btnCreateGroup').button('reset');
      }
    });
  }
  function updateGroup(data, id) {
    $.ajax({
      type: 'POST',
      url: route('groups.update', id),
      data: data,
      processData: false,
      contentType: false,
      success: function success(result) {
        var group = result.data.group;
        if (result.data.conversation) {
          setSentOrReceivedMessage(result.data.conversation);
        }
        displayToastr('Success', 'success', result.message);
        // Update Group Details
        performActionAfterUpdatingGroup(group);
      },
      error: function error(result) {
        displayToastr('Error', 'error', result.responseJSON.message);
      }
    });
  }
  function performActionAfterUpdatingGroup(group) {
    var groupId = group.id;
    $('#groupDetailsDescription-' + groupId).text(group.description);
    if (!group.description) {
      $('#groupDetailsDescription-' + groupId).text('No description added yet...');
    }
    $('#groupDetailsName-' + groupId).text(group.name);
    $('#groupDetailsImage-' + groupId).attr('src', group.photo_url);
    $('.group-list-' + groupId).find('img').attr('src', group.photo_url);
    $('.group-list-' + groupId).find('.add-user-contact-name').text(group.name);
    $('.contact-title').text(group.name);
    $('.chat-header-img').attr('src', group.photo_url);
    $('.chat__person-box--active').find('.chat__person-box-name').text(group.name);
    $('.chat__person-box--active').find('.chat__person-box-avtar img').attr('src', group.photo_url);
    if (group.group_type === 2) {
      $('.group-profile-image').append(closedGroupIcon);
    } else {
      $('.group-profile-image').find('.closed-group-badge').remove();
    }
    if (group.privacy === 2) {
      $('.group-profile-image').append(privateGroupIcon);
    } else {
      $('.group-profile-image').find('.private-group-badge').remove();
    }
    $('#editOldGroup').modal('hide');
  }
  $('#createGroupForm').on('submit', function (e) {
    e.preventDefault();
    var id = $('#groupId').val();
    var data = new FormData($(this)[0]);
    data["delete"]('users');
    var groupMembers = JSON.parse($('#selectedGroupMembers').val());
    if (groupMembers.length < 1) {
      displayToastr('Error', 'error', 'Add any one member.');
      return false;
    }
    $.each(groupMembers, function (index, val) {
      data.append('users[]', val);
    });
    if (!id) {
      $('#btnCreateGroup').attr('disabled', true);
      $('#btnCreateGroup').button('loading');
      createGroup(data);
      return;
    }
    updateGroup(data, id);
  });
  $('#editGroupForm').on('submit', function (e) {
    e.preventDefault();
    var id = $('#editGroupId').val();
    var data = new FormData($(this)[0]);
    $('#btnEditGroup').attr('disabled', true);
    $('#btnEditGroup').button('loading');
    updateGroup(data, id);
  });

  /** Edit Group */
  $(document).on('click', '.edit-group', function (e) {
    var id = $(e.currentTarget).data('id');
    $.ajax({
      type: 'GET',
      url: route('group.show', id),
      success: function success(result) {
        if (result.success) {
          var group = result.data;
          $('#editGroupId').val(group.id);
          $('#editGroupName').val(group.name);
          $('#editGroupDesc').val(group.description);
          if (group.group_type === 2) {
            $('#editGroupTypeClose').iCheck('check');
          }
          if (group.privacy === 2) {
            $('#editGroupPrivate').iCheck('check');
          }
          $('#editGroupPhotoPreview').attr('src', group.photo_url);
          if (group.my_role !== 2) {
            $('.div-group-privacy').addClass('d-none');
            $('.div-group-type').addClass('d-none');
          }
          $('#editOldGroup').modal('show');
        }
      },
      error: function error(_error10) {
        displayToastr('Error', 'error', _error10.responseJSON.message);
      }
    });
    e.stopPropagation();
  });
  $('#createNewGroup').on('hidden.bs.modal', function () {
    $('#groupId').val('');
    $('#groupTypeOpen').iCheck('check');
    $('#groupPublic').iCheck('check');
    $('#groupMembers').parent().show();
    $('.div-group-privacy').removeClass('d-none');
    $('.div-group-type').removeClass('d-none');
    $('#btnCreateGroup').removeAttr('disabled');
    $('#btnCreateGroup').button('reset');
    resetModalForm('#createGroupForm', '#groupValidationErrorsBox');
    window.livewire.emit('resetGroupMemberSearch');
    addedMembers = [];
    $('#groupMembersList').show();
    $('.added-group-members-list').find('li').remove();
    $('.no-member-added').removeClass('d-none');
  });
  $('#editOldGroup').on('hidden.bs.modal', function () {
    $('#editGroupId').val('');
    $('#editGroupTypeOpen').iCheck('check');
    $('#editGroupPublic').iCheck('check');
    $('.div-group-privacy').removeClass('d-none');
    $('.div-group-type').removeClass('d-none');
    $('#btnEditGroup').removeAttr('disabled');
    $('#btnEditGroup').button('reset');
    resetModalForm('#editGroupForm', '#editGroupValidationErrorsBox');
  });

  // Fill members to group members modal
  $(document).on('click', '.btn-add-members', function (e) {
    e.preventDefault();
    var groupId = $(e.currentTarget).data('group-id');
    $.ajax({
      type: 'GET',
      url: route('group.show', groupId),
      success: function success(result) {
        if (result.success) {
          var _data3 = result.data;
          addedMembers = _data3.users;
          if (_data3.users_with_trashed.length > 0) {
            $('modalAddGroupMembers').find('.no-member-added').removeClass('d-none').addClass('d-none');
          }
          var noMembersTemplate = $.templates('#tmplNoGroupMembers');
          var template = $.templates('#tmplAddedGroupMembers');
          var users = _data3.users_with_trashed;
          $('.btn-add-members-to-group').attr('data-group-id', groupId);
          var htmlOutput = template.render(users);
          $('#modalAddGroupMembers').find('.added-group-members-list').html(htmlOutput);
          htmlOutput = noMembersTemplate.render({});
          $('#modalAddGroupMembers').find('.added-group-members-list').append(htmlOutput);
          window.livewire.emit('searchEditGroupMembers', '', _data3.users);
          $('#modalAddGroupMembers').modal('show');
          $('#selectedGroupMembersForEdit').val(JSON.stringify(addedMembers));
        }
      },
      error: function error(_error11) {
        displayToastr('Error', 'error', _error11.responseJSON.message);
      }
    });
  });
  window.isMembersInGroup = function (groupMembers, memberId) {
    if ($.inArray(memberId, groupMembers) === -1) {
      return false;
    }
    return true;
  };

  /** Add Members to Group */
  $(document).on('click', '.btn-add-members-to-group', function (e) {
    e.preventDefault();
    var allMembers = JSON.parse($('#selectedGroupMembersForEdit').val());
    var members = $(allMembers).not(groupMembers).get();
    if (members.length === 0) {
      return;
    }
    $(this).button('loading');
    $(this).attr('disabled');
    $.ajax({
      type: 'PUT',
      url: route('groups-group.add-members', currentSelectedGroupId),
      data: {
        members: members
      },
      success: function success(result) {
        if (result.success) {
          addMembersToGroup(currentSelectedGroupId, result.data.group);
          $.merge(groupMembers, members);
          setSentOrReceivedMessage(result.data.conversation);
          displayToastr('Success', 'success', result.message);
        }
      },
      error: function error(_error12) {
        displayToastr('Error', 'error', _error12.responseJSON.message);
      },
      complete: function complete() {
        $('#modalAddGroupMembers').modal('hide');
      }
    });
  });
  $('#modalAddGroupMembers').on('hidden.bs.modal', function () {
    $('.btn-add-members-to-group').removeAttr('disabled');
    $('.btn-add-members-to-group').button('reset');
    addedMembers = [];
  });

  /** Remove Member from Group */
  $(document).on('click', '.remove-member-from-group', function (e) {
    e.preventDefault();
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'Remove member from this group ?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, remove it!'
    }).then(function (result) {
      if (result.value) {
        var id = $(e.currentTarget).data('member-id');
        var groupId = $(e.currentTarget).data('group-id');
        $.ajax({
          type: 'DELETE',
          url: route('group-from-member-remove', {
            group: groupId,
            user: id
          }),
          success: function success(result) {
            if (result.success) {
              setSentOrReceivedMessage(result.data);
              removeMemberFromGroup(groupId, id);
              $('#modalAddGroupMembers').modal('hide');
              displayToastr('Success', 'success', result.message);
            }
          },
          error: function error(_error13) {
            displayToastr('Error', 'error', _error13.responseJSON.message);
          }
        });
      }
    });
    e.stopPropagation();
  });

  /** Make Member to Group Admin */
  $(document).on('click', '.make-member-to-group-admin', function (e) {
    e.preventDefault();
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'Make this member to Admin?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes'
    }).then(function (result) {
      if (result.value) {
        var id = $(e.currentTarget).data('member-id');
        var groupId = $(e.currentTarget).data('group-id');
        $.ajax({
          type: 'PUT',
          url: route('groups.members.make-admin', {
            group: groupId,
            user: id
          }),
          success: function success(result) {
            if (result.success) {
              setSentOrReceivedMessage(result.data);
              myRoleInGroup = 2;
              updateGroupMemberRole(groupId, id, true);
              displayToastr('Success', 'success', result.message);
            }
          },
          error: function error(_error14) {
            displayToastr('Error', 'error', _error14.responseJSON.message);
          }
        });
      }
    });
    e.stopPropagation();
  });

  /** Dismiss Group Admin  */
  $(document).on('click', '.dismiss-admin-member', function (e) {
    e.preventDefault();
    swalDelete.fire({
      title: 'Are you sure?',
      html: 'Dismiss this member from Admin?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes'
    }).then(function (result) {
      if (result.value) {
        var id = $(e.currentTarget).data('member-id');
        var groupId = $(e.currentTarget).data('group-id');
        $.ajax({
          type: 'PUT',
          url: route('groups.members.dismiss-as-admin', {
            group: groupId,
            user: id
          }),
          success: function success(result) {
            if (result.success) {
              setSentOrReceivedMessage(result.data);
              myRoleInGroup = 1;
              updateGroupMemberRole(groupId, id, false);
              displayToastr('Success', 'success', result.message);
            }
          },
          error: function error(_error15) {
            displayToastr('Error', 'error', _error15.responseJSON.message);
          }
        });
      }
    });
    e.stopPropagation();
  });

  /** Leave Group  */
  $(document).on('click', '.btn-leave-from-group', function (e) {
    e.preventDefault();
    swalDelete.fire({
      title: Lang.get('messages.placeholder.are_you_sure'),
      html: Lang.get('messages.placeholder.want_to_leave_group'),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes!'
    }).then(function (result) {
      if (result.value) {
        var groupId = $(e.currentTarget).data('group-id');
        $.ajax({
          type: 'DELETE',
          url: route('groups.leave', groupId),
          success: function success(result) {
            if (result.success) {
              window.livewire.emit('searchGroup');
              membersCountArr[groupId] -= 1;
              $('.members-count').text(membersCountArr[groupId]);
              Echo.leave('group.' + groupId); // Unsubscribe to channel
              $('.typing').remove();
              displayToastr('Success', 'success', result.message);
              setSentOrReceivedMessage(result.data);
              $('.btn-leave-from-group').parent().remove();
              var deleteGroup = prepareDeleteGroupButton(groupId);
              $('.group-member-' + loggedInUserId).remove();
              $('.group-profile-data').append(deleteGroup);
              $('.chat__area-text').remove();
            }
          },
          error: function error(_error16) {
            displayToastr('Error', 'error', _error16.message);
          }
        });
      }
    });
    e.stopPropagation();
  });

  /** Remove Member From Group  */
  $(document).on('click', '.btn-delete-group', function (e) {
    e.preventDefault();
    swalDelete.fire({
      title: Lang.get('messages.placeholder.are_you_sure'),
      html: Lang.get('messages.placeholder.want_to_remove_group'),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes!'
    }).then(function (result) {
      if (result.value) {
        var groupId = $(e.currentTarget).data('group-id');
        $.ajax({
          type: 'DELETE',
          url: route('group-remove', groupId),
          success: function success(result) {
            if (result.success) {
              window.livewire.emit('searchGroup');
              Echo.leave('group.' + groupId); // Unsubscribe to group message event
              $('.chat__person-box--active').remove();
              addNoConversationIndicator();
              $('.btn-leave-from-group').parent().remove();
              displayToastr('Success', 'success', result.message);
            }
          },
          error: function error(_error17) {
            displayToastr('Error', 'error', _error17.message);
          }
        });
      }
    });
    e.stopPropagation();
  });
  function prepareGroupAdminText(memberId, groupId) {
    var addAdminText = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
    if (addAdminText) {
      return '<a class="dropdown-item make-member-to-group-admin" href="#" data-member-id="' + memberId + '" data-group-id="' + groupId + '" id="makeAdmin-' + memberId + '">Make Admin</a>';
    }
    return '<a class="dropdown-item dismiss-admin-member" href="#" data-member-id="' + memberId + '" data-group-id="' + groupId + '" id="dismissAdmin-' + memberId + '">Dismiss As Admin</a>  ';
  }
  function prepareDeleteGroupButton(groupId) {
    return '<div class="chat-profile__column pt-1"> <a href="#" class="btn btn-danger btn-delete-group" data-group-id="' + groupId + '">Delete Group</a></div>';
  }
  window.updateUnreadMessageCount = function (countOfConversationRead) {
    totalUnreadConversations -= countOfConversationRead;
    if (totalUnreadConversations === 0 || totalUnreadConversations < 1) {
      $('title').text('Conversations | ' + appName);
      return;
    }
    var messageString = totalUnreadConversations > 99 ? '(99+)' : '(' + totalUnreadConversations + ')';
    $('title').text(messageString + ' | Conversations | ' + appName);
  };
  window.removeMediaFromProfileBar = function (id) {
    $('#mediaProfile-' + id).remove();
    var length = $('.chat-profile__media-container').children().length;
    if (length == 1 || length == 0) {
      $('.no-photo-found').show();
    }
  };
  window.loadTooltip = function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  };

  /** Select Checkbox when div clicked */
  $(document).on('click', '.group-members-list-chat-select__list-item', function (e) {
    var selector = $(this).find('.select-group-members');
    if ($(this).hasClass('opacity-07')) {
      return;
    }
    var isChecked = selector.prop('checked');
    if (!isChecked) {
      $(this).find('.select-group-members').iCheck('check');
    } else {
      $(this).find('.select-group-members').iCheck('uncheck');
    }
  });
  $(document).on('click', '.replay-close-btn', function (e) {
    removeReplayBox();
  });
  function removeReplayBox() {
    $('.chat__text-preview').remove();
    $('.chat-conversation').removeClass('chat-conversation-88');
    removeLocalStorageItem('reply');
  }
  function prepareReplyBox(data) {
    var helpers = {
      getURLFromMessageType: getURLFromMessageType
    };
    var template = $.templates('#tmplReplayBox');
    var html = template.render(data, helpers);
    $('.chat__area-text').before(html);
    $('.chat-conversation').addClass('chat-conversation-88');
    textMessageEle.data('emojioneArea').setFocus();
    return data;
  }
  $(document).on('click', '.msg-replay', function (e) {
    e.preventDefault();
    e.stopPropagation();
    removeReplayBox();
    var data = {};
    var receiver = $(this).data('sender');
    var userId = selectedContactId;
    var selfReply = $(this).data('self-reply');
    var messageType = $(this).data('message-type');
    if (selfReply) {
      receiver = 'You';
    }
    data.id = $(this).data('message-id');
    data.message = $(this).data('message');
    data.receiver = receiver;
    data.user_id = userId;
    data.messageType = messageType;
    prepareReplyBox(data);
    setLocalStorageItem('reply', JSON.stringify(data));
  });
  function getURLFromMessageType(type) {
    if (type == 2) {
      return pdfURL;
    } else if (type == 3) {
      return docsURL;
    } else if (type == 4) {
      return audioURL;
    } else if (type == 5) {
      return videoURL;
    } else if (type == 6) {
      return youtubeURL;
    } else if (type == 7) {
      return textURL;
    } else if (type == 8) {
      return xlsURL;
    }
  }
  function triggerConversationClick() {
    var conversationEle = $(document).find('#user-' + conversationId);
    if (conversationEle.length) {
      conversationEle.trigger('click');
    } else {
      setTimeout(triggerConversationClick, 500);
    }
  }
  if (conversationId !== '') {
    setTimeout(triggerConversationClick, 500);
  }

  /**
   * This handler retrieves the images from the clipboard as a base64 string and returns it in a callback.
   *
   * @param pasteEvent
   * @param callback
   */
  function retrieveImageFromClipboardAsBase64(pasteEvent, callback, imageFormat) {
    if (pasteEvent.clipboardData == false) {
      if (typeof callback == 'function') {
        callback(undefined);
      }
    }
    var items = pasteEvent.clipboardData.items;
    if (items == undefined) {
      if (typeof callback == 'function') {
        callback(undefined);
      }
    }
    var _loop = function _loop() {
      // Skip content if not image
      if (items[i].type.indexOf('image') == -1) return "continue";
      // Retrieve image on clipboard as blob
      var blob = items[i].getAsFile();

      // Create an abstract canvas and get context
      var mycanvas = document.createElement('canvas');
      var ctx = mycanvas.getContext('2d');

      // Create an image
      var img = new Image();

      // Once the image loads, render the img on the canvas
      img.onload = function () {
        // Update dimensions of the canvas with the dimensions of the image
        mycanvas.width = this.width;
        mycanvas.height = this.height;

        // Draw the image
        ctx.drawImage(img, 0, 0);

        // Execute callback with the base64 URI of the image
        if (typeof callback == 'function') {
          callback(mycanvas.toDataURL(imageFormat || 'image/png'));
        }
      };

      // Crossbrowser support for URL
      var URLObj = window.URL || window.webkitURL;

      // Creates a DOMString containing a URL representing the object given in the parameter
      // namely the original Blob
      img.src = URLObj.createObjectURL(blob);
    };
    for (var i = 0; i < items.length; i++) {
      var _ret = _loop();
      if (_ret === "continue") continue;
    }
  }
  window.addEventListener('paste', function (e) {
    // Handle the event
    retrieveImageFromClipboardAsBase64(e, function (imageDataBase64) {
      // If there's an image, open it in the browser as a new window :)
      if (!(selectedContactId > 0 || selectedContactId != '')) {
        return false;
      }
      if (imageDataBase64) {
        var template = $.templates('#copyPastImgTmplt');
        var imgHtml = template.render({
          'url': imageDataBase64
        });
        $('#imageCanvas').append(imgHtml);
        $('#copyImageModal').modal('show');
      }
    });
  }, false);
  $(document).on('click', '#sendImages', function (e) {
    var imagesArr = [];
    var imagesArrHtml = $('#imageCanvas').find('.img-thumbnail');
    $.each(imagesArrHtml, function (index, value) {
      imagesArr.push($(this).attr('src'));
    });
    var data = {
      'to_id': selectedContactId,
      'message_type': 1,
      'images': imagesArr,
      'is_group': $('.chat__person-box--active').data('is_group')
    };
    $.ajax({
      type: 'POST',
      url: route('image-upload'),
      data: data,
      success: function success(data) {
        $.each(data.data, function (index, value) {
          setSentOrReceivedMessage(value);
        });
        resetImageCanvas();
        $('#copyImageModal').modal('hide');
      },
      error: function error(_error18) {
        displayToastr('Error', 'error', _error18.responseJSON.message);
      }
    });
  });
  $(document).on('click', '.remove-img', function (e) {
    $(this).parent().remove();
  });
  $('#copyImageModal').on('hidden.bs.modal', function () {
    resetImageCanvas();
  });
  window.resetImageCanvas = function () {
    $('#imageCanvas').html('');
  };
});

//Dropzon code
var myDropzone = '';
var sendMsgFiles = [];
$('#submit-all').hide();
$('#cancel-upload-file').hide();
window.Dropzone.options.dropzone = {
  thumbnailWidth: 125,
  acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.mp4,.mkv,.avi,.txt,.mp3,.ogg,.wav,.aac,.alac,.zip,.rar',
  timeout: 50000,
  autoProcessQueue: false,
  parallelUploads: 10,
  // Number of files process at a time (default 2)
  addRemoveLinks: true,
  dictRemoveFile: '<i class="fa fa-trash-o" title="Remove" style="color: indianred;"></i>',
  uploadMultiple: true,
  init: function init() {
    var submitButton = document.querySelector('#submit-all');
    var cancelButton = document.querySelector('#cancel-upload-file');
    myDropzone = this; // closure

    submitButton.addEventListener('click', function () {
      $('.dz-progress').show();
      myDropzone.processQueue(); // Tell Dropzone to process all queued files.
    });

    cancelButton.addEventListener('click', function () {
      myDropzone.removeAllFiles(true);
      $('#fileUpload').modal('toggle');
    });

    // show the submit button only when files are dropped here:
    this.on('addedfile', function () {
      $('#submit-all,#cancel-upload-file').show();
      $('.dz-progress').hide();
    });
    this.on('removedfile', function () {
      if (this.getQueuedFiles().length === 0) {
        $('#submit-all,#cancel-upload-file').hide();
      }
    });
  },
  complete: function complete(file) {
    if (this.getQueuedFiles().length > 0) {
      this.processQueue();
    }
    this.files.push(file);
    this.on('queuecomplete', function () {
      $('#fileUpload').modal('toggle');
      this.removeAllFiles(true);
      sendMsgFiles = [];
    });
  },
  success: function success(file, response) {
    $.each(response.data, function (index, value) {
      var toId = $('#toId').val();
      var isGroup = $('.chat__person-box--active').data('is_group');
      var data = {
        to_id: toId,
        message: value.attachment,
        message_type: value.message_type,
        file_name: value.file_name,
        is_group: isGroup ? 1 : 0
      };
      if ($.inArray(value.unique_code, sendMsgFiles) === -1) {
        storeMessage(data);
        sendMsgFiles.push(value.unique_code);
      }
    });
  },
  error: function error(file, response) {
    if (_typeof(response) === 'object') {
      response = response.hasOwnProperty('message') ? response.message : 'There is some error, Please try after some time';
    }
    displayToastr('Error', 'error', response);
    var fileRef;
    return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : void 0;
  }
};
$('#fileUpload').on('hidden.bs.modal', function () {
  $('#submit-all,#cancel-upload-file').hide();
  myDropzone.removeAllFiles(true);
});
$(document).on('mouseleave', '.chat__person-box', function () {
  $('.more-btn-conversation-item, .chat-item-menu').removeClass('show');
});
$(document).on('click', '.chat__person-box-msg-time', function (e) {
  e.stopPropagation();
});
$(document).on('click', '.action-dropdown .hide-ele', function () {
  if (!$('.action-dropdown-menu, .action-dropdown').hasClass('show')) {
    $('.msgDropdown').trigger('click.bs.dropdown');
  }
});
var changedToArchivedConversations = [];
var changedToConversations = [];
var conversationsContainer = document.getElementById('chatPeopleBody');
var containerHeight = $(conversationsContainer).outerHeight();
var isLoaded = false;
var offset = 10;
var chatPeopleBodyEle = $('#chatPeopleBody');
var loadMoreBtn = $('#loadMoreConversationBtn').clone();
$(document).on('click', '.load-more-conversation', function () {
  $(this).parent().remove();
  var $loader = $(conversationsContainer).find('.chat__people-body-loader').clone();
  $loader.addClass('ajax-loading');
  $(chatPeopleBodyEle).append($loader);
  $loader.show();
  isLoaded = true;
  $.ajax({
    type: 'GET',
    url: route('conversations-list', {
      type: conversationType
    }),
    data: {
      'offset': offset
    },
    dataType: 'json',
    success: function success(data) {
      $loader.remove();
      if (data.data.conversations.length > 0) {
        isLoaded = false;
        offset += 10;
        if (data.success) {
          $('#infyLoader').hide();
          var latestConversations = data.data.conversations;
          chatPeopleBodyEle.append(latestConversations.map(prepareContacts).join(''));
          searchUsers();
          loadTooltip();
          if (data.data.conversations.length > 0) {
            chatPeopleBodyEle.append(loadMoreBtn);
            loadMoreBtn.show();
            loadMoreBtn.addClass('active');
          }
        }
      }
    },
    error: function error(_error19) {
      console.log(_error19);
    }
  });
});
var archiveContainer = document.getElementById('archivePeopleBody');
var archiveHeight = $(archiveContainer).outerHeight();
var isArchiveLoaded = false;
var archiveOffset = 10;
var archivePeopleBodyEle = $('#archivePeopleBody');
var loadMoreArchiverBtn = $('#loadMoreArchiverConversationBtn').clone();
$(document).on('click', '.load-more-archive-conversation', function () {
  $(this).parent().remove();
  var $loader = $(conversationsContainer).find('.chat__people-body-loader').clone();
  $loader.addClass('ajax-loading');
  $(archivePeopleBodyEle).append($loader);
  $loader.show();
  isArchiveLoaded = true;
  //GET Archive conversations list
  $.ajax({
    type: 'GET',
    url: route('archive-conversations', {
      type: conversationType
    }),
    data: {
      'offset': archiveOffset
    },
    dataType: 'json',
    success: function success(data) {
      $loader.remove();
      isArchiveLoaded = false;
      archiveOffset += 10;
      if (data.success) {
        var archiveConversations = data.data.conversations;
        archivePeopleBodyEle.append(archiveConversations.map(prepareContacts).join(''));
        archivePeopleBodyEle.find('.chat__person-box-archive').each(function () {
          $(this).text('Unarchive Chat');
        });
        searchUsers();
        if (data.data.conversations.length > 0) {
          archivePeopleBodyEle.append(loadMoreArchiverBtn);
          loadMoreArchiverBtn.addClass('active');
          loadMoreArchiverBtn.show();
        }
      }
    },
    error: function error(_error20) {
      console.log(_error20);
    }
  });
});
}();
/******/ })()
;