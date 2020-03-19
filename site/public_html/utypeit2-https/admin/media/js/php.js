function urldecode (str) {
  // http://kevin.vanzonneveld.net
  // +   original by: Philip Peterson
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +      input by: AJ
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: travc
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Lars Fischer
  // +      input by: Ratheous
  // +   improved by: Orlando
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +      bugfixed by: Rob
  // +      input by: e-mike
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
  // %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
  // %        note 2: pages served as UTF-8
  // *     example 1: urldecode('Kevin+van+Zonneveld%21');
  // *     returns 1: 'Kevin van Zonneveld!'
  // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
  // *     returns 2: 'http://kevin.vanzonneveld.net/'
  // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
  // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
  return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}
function str_replace (search, replace, subject, count) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Gabriel Paderni
  // +   improved by: Philip Peterson
  // +   improved by: Simon Willison (http://simonwillison.net)
  // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   bugfixed by: Anton Ongson
  // +      input by: Onno Marsman
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    tweaked by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   input by: Oleg Eremeev
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Oleg Eremeev
  // %          note 1: The count parameter must be passed as a string in order
  // %          note 1:  to find a global variable in which the result will be given
  // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
  // *     returns 1: 'Kevin.van.Zonneveld'
  // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
  // *     returns 2: 'hemmo, mars'
  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp).split(f[j]).join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}
function substr (str, start, len) {
  // Returns part of a string
  //
  // version: 909.322
  // discuss at: http://phpjs.org/functions/substr
  // +     original by: Martijn Wieringa
  // +     bugfixed by: T.Wild
  // +      tweaked by: Onno Marsman
  // +      revised by: Theriault
  // +      improved by: Brett Zamir (http://brett-zamir.me)
  // %    note 1: Handles rare Unicode characters if 'unicode.semantics' ini (PHP6) is set to 'on'
  // *       example 1: substr('abcdef', 0, -1);
  // *       returns 1: 'abcde'
  // *       example 2: substr(2, 0, -6);
  // *       returns 2: false
  // *       example 3: ini_set('unicode.semantics',  'on');
  // *       example 3: substr('a\uD801\uDC00', 0, -1);
  // *       returns 3: 'a'
  // *       example 4: ini_set('unicode.semantics',  'on');
  // *       example 4: substr('a\uD801\uDC00', 0, 2);
  // *       returns 4: 'a\uD801\uDC00'
  // *       example 5: ini_set('unicode.semantics',  'on');
  // *       example 5: substr('a\uD801\uDC00', -1, 1);
  // *       returns 5: '\uD801\uDC00'
  // *       example 6: ini_set('unicode.semantics',  'on');
  // *       example 6: substr('a\uD801\uDC00z\uD801\uDC00', -3, 2);
  // *       returns 6: '\uD801\uDC00z'
  // *       example 7: ini_set('unicode.semantics',  'on');
  // *       example 7: substr('a\uD801\uDC00z\uD801\uDC00', -3, -1)
  // *       returns 7: '\uD801\uDC00z'
  // Add: (?) Use unicode.runtime_encoding (e.g., with string wrapped in "binary" or "Binary" class) to
  // allow access of binary (see file_get_contents()) by: charCodeAt(x) & 0xFF (see https://developer.mozilla.org/En/Using_XMLHttpRequest ) or require conversion first?
  var i = 0,
    allBMP = true,
    es = 0,
    el = 0,
    se = 0,
    ret = '';
  str += '';
  var end = str.length;

  // BEGIN REDUNDANT
  this.php_js = this.php_js || {};
  this.php_js.ini = this.php_js.ini || {};
  // END REDUNDANT
  switch ((this.php_js.ini['unicode.semantics'] && this.php_js.ini['unicode.semantics'].local_value.toLowerCase())) {
  case 'on':
    // Full-blown Unicode including non-Basic-Multilingual-Plane characters
    // strlen()
    for (i = 0; i < str.length; i++) {
      if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
        allBMP = false;
        break;
      }
    }

    if (!allBMP) {
      if (start < 0) {
        for (i = end - 1, es = (start += end); i >= es; i--) {
          if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
            start--;
            es--;
          }
        }
      } else {
        var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
        while ((surrogatePairs.exec(str)) != null) {
          var li = surrogatePairs.lastIndex;
          if (li - 2 < start) {
            start++;
          } else {
            break;
          }
        }
      }

      if (start >= end || start < 0) {
        return false;
      }
      if (len < 0) {
        for (i = end - 1, el = (end += len); i >= el; i--) {
          if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
            end--;
            el--;
          }
        }
        if (start > end) {
          return false;
        }
        return str.slice(start, end);
      } else {
        se = start + len;
        for (i = start; i < se; i++) {
          ret += str.charAt(i);
          if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
            se++; // Go one further, since one of the "characters" is part of a surrogate pair
          }
        }
        return ret;
      }
      break;
    }
    // Fall-through
  case 'off':
    // assumes there are no non-BMP characters;
    //    if there may be such characters, then it is best to turn it on (critical in true XHTML/XML)
  default:
    if (start < 0) {
      start += end;
    }
    end = typeof len === 'undefined' ? end : (len < 0 ? len + end : len + start);
    // PHP returns false if start does not fall within the string.
    // PHP returns false if the calculated end comes before the calculated start.
    // PHP returns an empty string if start and end are the same.
    // Otherwise, PHP returns the portion of the string from start to end.
    return start >= str.length || start < 0 || start > end ? !1 : str.slice(start, end);
  }
  return undefined; // Please Netbeans
}
function htmlspecialchars (string, quote_style, charset, double_encode) {
  // http://kevin.vanzonneveld.net
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Nathan
  // +   bugfixed by: Arno
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +      input by: felix
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: charset argument not supported
  // *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
  // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
  // *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
  // *     returns 2: 'ab"c&#039;d'
  // *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
  // *     returns 3: 'my &quot;&entity;&quot; is still here'
  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined' || quote_style === null) {
    quote_style = 2;
  }
  string = string.toString();
  if (double_encode !== false) { // Put this first to avoid double-encoding
    string = string.replace(/&/g, '&amp;');
  }
  string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      }
      else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/'/g, '&#039;');
  }
  if (!noquotes) {
    string = string.replace(/"/g, '&quot;');
  }

  return string;
}
function html_entity_decode (string, quote_style) {
  // http://kevin.vanzonneveld.net
  // +   original by: john (http://www.jd-tech.net)
  // +      input by: ger
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // +   improved by: marc andreu
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +      input by: Ratheous
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Nick Kolosov (http://sammy.ru)
  // +   bugfixed by: Fox
  // -    depends on: get_html_translation_table
  // *     example 1: html_entity_decode('Kevin &amp; van Zonneveld');
  // *     returns 1: 'Kevin & van Zonneveld'
  // *     example 2: html_entity_decode('&amp;lt;');
  // *     returns 2: '&lt;'
  var hash_map = {},
    symbol = '',
    tmp_str = '',
    entity = '';
  tmp_str = string.toString();

  if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
    return false;
  }

  // fix &amp; problem
  // http://phpjs.org/functions/get_html_translation_table:416#comment_97660
  delete(hash_map['&']);
  hash_map['&'] = '&amp;';

  for (symbol in hash_map) {
    entity = hash_map[symbol];
    tmp_str = tmp_str.split(entity).join(symbol);
  }
  tmp_str = tmp_str.split('&#039;').join("'");

  return tmp_str;
}
function strtolower (str) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Onno Marsman
  // *     example 1: strtolower('Kevin van Zonneveld');
  // *     returns 1: 'kevin van zonneveld'
  return (str + '').toLowerCase();
}
function ucwords (str) {
  // http://kevin.vanzonneveld.net
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Waldo Malqui Silva
  // +   bugfixed by: Onno Marsman
  // +   improved by: Robin
  // +      input by: James (http://www.james-bell.co.uk/)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // *     example 1: ucwords('kevin van  zonneveld');
  // *     returns 1: 'Kevin Van  Zonneveld'
  // *     example 2: ucwords('HELLO WORLD');
  // *     returns 2: 'HELLO WORLD'
  return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
    return $1.toUpperCase();
  });
}
function asort (inputArr, sort_flags) {
  // http://kevin.vanzonneveld.net
  // +   original by: Brett Zamir (http://brett-zamir.me)
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   input by: paulo kuong
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Adam Wallner (http://web2.bitbaro.hu/)
  // +   improved by: Theriault
  // %        note 1: SORT_STRING (as well as natsort and natcasesort) might also be
  // %        note 1: integrated into all of these functions by adapting the code at
  // %        note 1: http://sourcefrog.net/projects/natsort/natcompare.js
  // %        note 2: The examples are correct, this is a new way
  // %        note 2: Credits to: http://javascript.internet.com/math-related/bubble-sort.html
  // %        note 3: This function deviates from PHP in returning a copy of the array instead
  // %        note 3: of acting by reference and returning true; this was necessary because
  // %        note 3: IE does not allow deleting and re-adding of properties without caching
  // %        note 3: of property position; you can set the ini of "phpjs.strictForIn" to true to
  // %        note 3: get the PHP behavior, but use this only if you are in an environment
  // %        note 3: such as Firefox extensions where for-in iteration order is fixed and true
  // %        note 3: property deletion is supported. Note that we intend to implement the PHP
  // %        note 3: behavior by default if IE ever does allow it; only gives shallow copy since
  // %        note 3: is by reference in PHP anyways
  // %        note 4: Since JS objects' keys are always strings, and (the
  // %        note 4: default) SORT_REGULAR flag distinguishes by key type,
  // %        note 4: if the content is a numeric string, we treat the
  // %        note 4: "original type" as numeric.
  // -    depends on: strnatcmp
  // -    depends on: i18n_loc_get_default
  // *     example 1: data = {d: 'lemon', a: 'orange', b: 'banana', c: 'apple'};
  // *     example 1: data = asort(data);
  // *     results 1: data == {c: 'apple', b: 'banana', d: 'lemon', a: 'orange'}
  // *     returns 1: true
  // *     example 2: ini_set('phpjs.strictForIn', true);
  // *     example 2: data = {d: 'lemon', a: 'orange', b: 'banana', c: 'apple'};
  // *     example 2: asort(data);
  // *     results 2: data == {c: 'apple', b: 'banana', d: 'lemon', a: 'orange'}
  // *     returns 2: true
  var valArr = [], valArrLen = 0,
    k, i, ret, sorter, that = this,
    strictForIn = false,
    populateArr = {};

  switch (sort_flags) {
  case 'SORT_STRING':
    // compare items as strings
    sorter = function (a, b) {
      return that.strnatcmp(a, b);
    };
    break;
  case 'SORT_LOCALE_STRING':
    // compare items as strings, based on the current locale (set with i18n_loc_set_default() as of PHP6)
    var loc = this.i18n_loc_get_default();
    sorter = this.php_js.i18nLocales[loc].sorting;
    break;
  case 'SORT_NUMERIC':
    // compare items numerically
    sorter = function (a, b) {
      return (a - b);
    };
    break;
  case 'SORT_REGULAR':
    // compare items normally (don't change types)
  default:
    sorter = function (a, b) {
      var aFloat = parseFloat(a),
        bFloat = parseFloat(b),
        aNumeric = aFloat + '' === a,
        bNumeric = bFloat + '' === b;
      if (aNumeric && bNumeric) {
        return aFloat > bFloat ? 1 : aFloat < bFloat ? -1 : 0;
      } else if (aNumeric && !bNumeric) {
        return 1;
      } else if (!aNumeric && bNumeric) {
        return -1;
      }
      return a > b ? 1 : a < b ? -1 : 0;
    };
    break;
  }

  // BEGIN REDUNDANT
  this.php_js = this.php_js || {};
  this.php_js.ini = this.php_js.ini || {};
  // END REDUNDANT
  strictForIn = this.php_js.ini['phpjs.strictForIn'] && this.php_js.ini['phpjs.strictForIn'].local_value && this.php_js.ini['phpjs.strictForIn'].local_value !== 'off';
  populateArr = strictForIn ? inputArr : populateArr;

  // Get key and value arrays
  for (k in inputArr) {
    if (inputArr.hasOwnProperty(k)) {
      valArr.push([k, inputArr[k]]);
      if (strictForIn) {
        delete inputArr[k];
      }
    }
  }

  valArr.sort(function (a, b) {
    return sorter(a[1], b[1]);
  });

  // Repopulate the old array
  for (i = 0, valArrLen = valArr.length; i < valArrLen; i++) {
    populateArr[valArr[i][0]] = valArr[i][1];
  }

  return strictForIn || populateArr;
}
