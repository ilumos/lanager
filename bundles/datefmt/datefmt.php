<?php
/*
  This is a standalone (except for mb_string) class to ultimately format dates & times.
  by Proger_XP | http://proger.i-forge.net/DateFmt/cWq | $Rev: 84 $
*/

class DateError extends Exception {
  function __construct($message) { return parent::__construct('DateFmt: '.$message); }
}

  class EDateLastPCRE extends DateError {
    public $pcreErrorCode;

    static function ThrowIfPcreFailed() {
      if (preg_last_error() != PREG_NO_ERROR) { throw new self; }
    }

    function __construct() {
      $this->pcreErrorCode = preg_last_error();
      parent::__construct('PCRE error; preg_last_error() returned '.$this->pcreErrorCode.
                          '. Check the encoding of your format string - it must be UTF-8.');
    }
  }

  class EDateLanguage extends DateError {
    public $iso2;

    function __construct($iso2) {
      $this->iso2 = $iso2;
      parent::__construct('Cannot load language '.$iso2);
    }
  }

  class EDateParse extends DateError { }

class DateFmt {
  static $selfTests;  // debug; use RunSelfTests(); is populated at the end of this file.
  static $languages;  // use LoadLangauge(); is populated at the end of this file.
  static $defaultLang = 'en';
  // 'w' is special and its format is array($minInterval, $maxValueForFAR).
  // e.g. array(7, 4) is >= 7 days after 'd' and 4 weeks to trigger IF-FAR[]. Must go
  // before the "physically" existing date component (e.g. "w"eeks before "d"ays).
  static $agoChars = array('s' => 60, 'i' => 60, 'h' => 24, 'w' => array(7, 4), 'd' => 30,
                           'o' => 12, 'y' => false);

  public $date, $now;
  public $strings;

  static function Format($str, $date = null, $language = null) {
    $prevEnc = mb_internal_encoding();
    mb_internal_encoding('UTF-8');

    try {
      $date = new self($date);
      $language and $date->LoadLanguage($language);
      $result = $date->FormatAs($str);

      mb_internal_encoding($prevEnc);
      return $result;
    } catch (Exception $e) {
      mb_internal_encoding($prevEnc);
      throw $e;
    }
  }

  function __construct($date = null) {
    $this->date = is_numeric($date) ? $date : time();
    $this->now = time();
    $this->LoadLanguage(self::$defaultLang);
  }

    function LoadLanguage($iso2OrArray) {
      if (!is_array($iso2OrArray)) {
        $lang = &self::$languages[ strtolower($iso2OrArray) ];
        if (!$lang) { throw new EDateLanguage($iso2OrArray); }
        $iso2OrArray = $lang;
      }

      $this->strings = $iso2OrArray + self::$languages['en'];
    }

  function FmtStr($name, $args) {
    return strtr($this->strings[$name], $args);
  }

  function FmtNum($number, $langName) {
    $inflections = $this->strings[$langName];
    if (is_array($inflections)) {
      $stem = array_shift($inflections);
      return self::FmtNumUsing($stem, $inflections,
                               (bool) $this->strings['number rolls'], $number);
    } else {
      return $inflections;
    }
  }

    static function FmtNumUsing($stem, $inflections, $numberRolls, $number) {
      $inflection = '';

      if ($number == 0) {
        $inflection = $inflections[0];
      } elseif ($number == 1) {
        $inflection = $inflections[1];
      } elseif ($number <= 4) {
        $inflection = $inflections[2];
      } elseif ($number <= 20 or !$numberRolls) {
        $inflection = $inflections[3];
      } else {  // 21 and over
        return self::FmtNumUsing( $stem, $inflections, $numberRolls, substr($number, 1) );
      }

      return $stem.$inflection;
    }

  static function FixFloat($float) {
    // PHP's floating point model can make evenly divisible numbers look like 0.999 -> 0.
    if ($float - (int) $float >= 0.9999) {
      return ceil($float);
    } else {
      return $float;
    }
  }

  function FormatAs($str) {
    $regExp = '~
                (?:
                    (AGO) (-SHORT)? (-AT)?
                  | (AT)
                )?
                \[
                  ( (?: \]\])+ | [^\]+]+ )
                \]
                (?:
                  (?: IF ( -FAR|>\d{1,4} ))
                  \[
                    ( (?:\]\])+|[^\]+]+ )
                  \]
                )?
                (AT|_)?
              ~xu';

    if (preg_match_all($regExp, $str, $parts, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
      $result = '';
      $prevPos = 0;

        foreach ($parts as $part) {
          // Note: mb_*() won't do here since preg_match* seem to capture
          //       ASCII offsets even in /u mode.
          $prev = substr($str, $prevPos, $part[0][1] - $prevPos);
          $prevPos = $part[0][1] + strlen( $part[0][0] );

          foreach ($part as &$piece) { $piece = $piece[0]; }
          $result .= $this->FormatNormal($prev) . $this->ParsePart($part);
        }

      return $result. $this->FormatNormal( substr($str, $prevPos) );
    } else {
      EDateLastPCRE::ThrowIfPcreFailed();
      return $this->FormatNormal($str);
    }
  }

    function ParsePart(&$part) {
      @list($reinsert, $isAgo, $isAgoShort, $isAgoAt, $isAt,
            $format, $ifRange, $ifFormat, $trailAtOrWord) = $part;

        $trailAt = $trailAtOrWord === 'AT';
        $noAgoWord = $trailAtOrWord === '_';

        if ($trailAt) { $isAt = $isAgoAt = true; }

      if ($isAgo) {
        $result = $this->FormatAGO($format, array('isShort' => (bool) $isAgoShort,
                                                  'wordForm' => $isAgoAt ? 'at' : '',
                                                  'ifFAR' => $ifRange === '-FAR',
                                                  'ifRange' => substr($ifRange, 1),
                                                  'ifFormat' => $ifFormat,
                                                  'noAgoWord' => $noAgoWord));
      } elseif ($isAt) {
        if ($ifRange) {
          throw new EDateParse('IF[] cannot be used with AT[] alone, only with AGO[] or AGO[]IF[]AT.');
        }

        $result = $this->FormatNormal($format, 'at');
      } else {
        return $this->FormatNormal($reinsert);
      }

      $trailAt and $result .= $this->strings['at-time'];
      return $result;
    }

  protected $normalFormatting;
  function FormatNormal($format, $wordForm = '') {
    $regExp = '~
                d(\#\#?) m(y?)
              | (h)(\#\#?) m(s?)
              | ( [dwysh] | mo? )
                (\#\#?)
              | (a\.m\.)
              | ( [dy] | mo? )
                (_ _?)
              ~ixu';

    $this->normalFormatting = compact('wordForm');
    return preg_replace_callback($regExp, array($this, 'NormalFormatter'), $format);
  }

    function NormalFormatter($match) {
      @list(, $date, $dateWithYear, $timeAmPM, $time, $timeWithSeconds, $intFormat,
            $intLeadZero, $amPM, $wordFormat, $wordLength) = $match;

      if ($date) {
        return $this->FormatDate($date === '##', (bool) $dateWithYear);
      } elseif ($time) {
        return $this->FormatTime($time === '##', (bool) $timeWithSeconds);
      } elseif ($intFormat) {
        $intLeadZero = $intLeadZero === '##';

        if ($intFormat !== 'h' and $intFormat !== 'H') {
          $intFormat = strtolower($intFormat);
          if ($intFormat === 'y' and $intLeadZero) { $intFormat = 'Y'; }
          $intFormat === 'm' and $intFormat = 'i';
          $intFormat === 'mo' and $intFormat = 'm';
        }

        $int = date($intFormat, $this->date);
        $int = ltrim($int, '0');
          $int === '' and $int = '0';
        $intLeadZero and $int = str_pad($int, 2, '0', STR_PAD_LEFT);

        return $int;
      } elseif ($amPM) {
        return $amPM[0] === 'a' ? mb_strtolower($this->amPM()) : $this->amPM();
      } elseif ($wordFormat) {
        $words = array('d' => array('days', date('w', $this->date)),
                       'm' => array('months', date('n', $this->date) - 1),
                       'y' => array('year', null));

        $lowcase = isset($words[ $wordFormat[0] ]);
          $lowcase or $wordFormat = strtolower($wordFormat);
        list($key, $index) = $words[ $wordFormat[0] ];

          $len = $wordLength === '__' ? 'full' : 'short';
          $form = $this->normalFormatting['wordForm'];  // e.g. "at".

          $key = "$len $key-$form";
          isset( $this->strings[$key] ) or $key = strtok($key, '-');

        $word = $this->strings[$key];
        isset($index) and $word = $word[$index];

        $lowcase and $word = mb_strtolower($word);
        return $word;
      }

      throw new DateError('Nothing matched in NormalFormatter()!');
    }

  function amPM() { return $this->strings[ date('A', $this->date) ]; }

  function FormatDate($dayLeadZero = false, $withYear = true) {
    $fmt = array();
    $fmt['d'] = date($dayLeadZero ? 'd' : 'j', $this->date);
    $fmt['m'] = date('m', $this->date);

    if ($withYear) {
      $fmt['y'] = date('Y', $this->date);
      return $this->FmtStr('date with year', $fmt);
    } else {
      return $this->FmtStr('date without year', $fmt);
    }
  }

    function FormatTime($hoursLeadZero = false, $withSeconds = false) {
      $fmt = array('%' => '');

        $fmt['m'] = date('i', $this->date);
        $this->strings['12 hour time'] and $fmt['%'] = $this->amPM();

      $mod = $hoursLeadZero ? 'h' : 'g';
      $this->strings['12 hour time'] or $mod = strtoupper($mod);
      $fmt['h'] = date($mod, $this->date);

      if ($withSeconds) {
        $fmt['s'] = date('s', $this->date);
        return $this->FmtStr('time with seconds', $fmt);
      } else {
        return $this->FmtStr('time without seconds', $fmt);
      }
    }

  // $options: isShort, wordForm, ifFAR, ifRange, ifFormat.
  function FormatAGO($format, $options = array()) {
    $exact = explode('.', $format);
    if (isset($exact[1])) {
      return $this->FormatExactAGO($exact, $options);
    } else {
      return $this->FormatFuzzyAGO($format, $options);
    }
  }

  // AGO[d.h]
  // $options: isShort, wordForm, noAgoWord, ifFAR, ifRange, ifFormat.
  function FormatExactAGO($ranges, $options = array()) {
    $chars = $ranges;

    foreach ($chars as &$char) {
      $char = strtolower($char);
      if (!isset(self::$agoChars[$char])) {
        $ranges = join('.', $ranges);
        throw new EDateParse("Unknown range character \"$char\" for exact AGO[$ranges].");
      }
    }

      $distance = $this->now - $this->date;
      $isInFuture = $distance < 0;
      $distance = abs($distance);

    $originalOrder = $chars;
    $chars = array_intersect_key(self::$agoChars, array_flip($chars));

    $formattedIF = $this->FormatIF($chars, $distance, $options);
    if ($formattedIF === null) {
      $result = $this->FormatExactAgoLimitedTo($chars, $distance, compact('originalOrder') + $options);

      if (!$options['noAgoWord']) {
        $result = $this->FmtStr($isInFuture ? 'in future' : 'ago', array('%' => $result));
      }
      return $result;
    } else {
      return $formattedIF;
    }
  }

    // $options: isShort, wordForm, originalOrder.
    function FormatExactAgoLimitedTo($chars, $distance, $options) {
      $components = $this->SplitDistanceIntoComponents($distance);

        foreach ($components as $i => $char) {
          if (!isset( $chars[$char[0]] )) {
            unset($components[$i]);
          }
        }
        $components = array_values($components);

      $this->ConvCompToSolidDistance($components);
      $components = $this->DistanceComponentsToNumWords($components, $options['isShort'], $options['wordForm']);

      // zeros are hidden unless all fields were 0s - then only the smallest one is output.
      $nonZeroComps = array_filter($components, array(__CLASS__, 'IsNonZeroDistance'));
      if (empty($nonZeroComps)) {
        while ($char = array_pop($options['originalOrder']) and !isset($components[$char])) { }
        $options['originalOrder'] = array($char);
      } else {
        $components = $nonZeroComps;
      }

      return $this->JoinDistanceComponentsInOrder($options['originalOrder'], $components);
    }

      function SplitDistanceIntoComponents($distance) {
        $comps = array();

        $remainder = $distance;
        foreach (self::$agoChars as $char => $length) {
          if (!$length) {
            break;
          } elseif (is_array($length)) {
            $dist = $remainder / $length[0];
          } else {
            $dist = $remainder;
            $remainder /= $length;
          }

          $comps[] = array($char, (int) $this->FixFloat($dist));
        }

        return $comps;
      }

      // On input, e.g. array( array('s', 176400), array('i', 2940), array('d', 2) ),
      // all distance components (176400, 2940 and 2) reflect the same distance (2 days and 1 hour)
      // - only in different units. This function converts them so they become
      // array( array('s', 0), array('i', 60), array('d', 2) ) - when summed together
      // they represent for original distance.
      function ConvCompToSolidDistance(&$comps) {
        foreach ($comps as $i => &$comp) {
          list($char, $dist) = $comp;

          $next = @$comps[$i + 1];
          if (isset($next)) {
            $toSubtract = $pseudoChar = null;

            foreach (self::$agoChars as $otherChar => $otherLength) {
              if ($otherChar === $next[0]) {
                break;
              } elseif ($otherChar === $char) {
                $toSubtract = $next[1];
              }

              if ($toSubtract !== null) {
                if (is_array($otherLength)) {
                  $pseudoChar = $otherLength[1];
                } elseif ($pseudoChar) {
                  $toSubtract *= $pseudoChar;
                  $pseudoChar = null;
                } else {
                  $toSubtract *= $otherLength;
                }
              }
            }

            $comp[1] -= $toSubtract;
          }
        }
      }

      function DistanceComponentsToNumWords($comps, $isShort, $wordForm) {
        $numWords = array();

        foreach ($comps as $comp) {
          list($char, $distance) = $comp;

          $key = 'AGO ';

            if (empty( $this->strings['force AT-form for AGO'] )) {
              $wordForm = $wordForm;
            } else {
              $wordForm = 'at';
            }
            $key .= ($isShort ? 'short ' : '')."$char-$wordForm";

          isset( $this->strings[$key] ) or $key = strtok($key, '-');

          $distance = self::FixFloat($distance);
          $intDistance = $distance = (int) $distance;

          $numWord = $this->FmtNum($distance, $key);
          $numWord = array('n' => $distance, '%' => $numWord, 'dist' => $intDistance);

          $numWords[$char] = $numWord;
        }

        return $numWords;
      }

      static function IsNonZeroDistance($comp) { return $comp['dist'] != 0; }

      function JoinDistanceComponentsInOrder($chars, $comps) {
        $result = '';

        // reverse because result is formed as "$new $prev", see the language strings.
        foreach (array_reverse($chars) as $char) {
          $numWord = &$comps[$char];
          if ($numWord) {
            $numWord['p'] = $result;
            unset($numWord['dist']);
            $result = $this->FmtStr($result === '' ? 'number word' : 'exact', $numWord);
          }
        }

        return $result;
      }

  // AGO[smh]
  // $options: isShort, wordForm, noAgoWord, ifFAR, ifRange, ifFormat.
  function FormatFuzzyAGO($format, $options = array()) {
    $distance = $this->now - $this->date;
    $isInFuture = $distance < 0;
    $distance = abs($distance);

    $chars = $this->NormAgoFormat($format, $distance, $useNearDays);

    $formattedIF = $this->FormatIF($chars, $distance, $options);
    if ($formattedIF === null) {
      list($char, $distance) = $this->FindNearestLimitedTo($chars, $distance);

      $useNearDays and $char = 'b'.((int) $distance);
      return $this->AgoStr($char, $distance, compact('isInFuture') + $options);
    } else {
      return $formattedIF;
    }
  }

    // $options: isShort, wordForm, isInFuture.
    function AgoStr($char, $distance, $options) {
      extract($options, EXTR_SKIP);

      $key = 'AGO ';

        if ($isInFuture and $char[0] === 'b') { $key .= 'future '; }
        $isShort and $key .= 'short ';

        empty( $this->strings['force AT-form for AGO'] ) or $wordForm = 'at';
        $key .= "$char-$wordForm";

      isset( $this->strings[$key] ) or $key = strtok($key, '-');

      if ($char[0] === 'b') {
        return $this->strings[$key];
      } else {
        $result = null;

        if (!$isShort) {
          if ($distance < 1 and $distance >= 0.45 and
                    isset( $this->strings['half '.$char] )) {
            $result = $this->strings['half '.$char];
          } elseif ($distance >= 1.45 and $distance < 2 and
                    isset( $this->strings['1.5 '.$char] )) {
            $result = $this->strings['1.5 '.$char];
          }
        }

          if ($result === null) {
            $distance = $this->FixFloat($distance);

              $prec = (($distance > 0.1 and $distance < 0.9) ? '1' : '0');
              $distance = sprintf("%1.{$prec}f", $distance);
              $distance = strtr($distance, '.', $this->strings['float delim']);

            $fmt = array('n' => $distance, '%' => $this->FmtNum($distance, $key));
            $result = $this->FmtStr('number word', $fmt);
          }

        if (!$noAgoWord) {
          $result = $this->FmtStr($isInFuture ? 'in future' : 'ago', array('%' => $result));
        }
        return $result;
      }
    }

    function NormAgoFormat($format, $distance, &$useNearDays) {
      $chars = $this->NormAgoFmtStr($format);
      $chars = array_flip( str_split($chars) );

      if (isset( $chars['b'] )) {
        $chars['d'] = true;
        unset($chars['b']);
        $useNearDays = $distance <= 3600 * 24 * 3;
      } else {
        $useNearDays = false;
      }

      $unkChars = array_diff_key($chars, self::$agoChars);
      if ($unkChars) {
        $s = count($unkChars) > 1 ? 's' : '';
        throw new EDateParse( "Unknown AGO[$format] character$s: ".
                             join(' ', array_keys($unkChars)) );
      }

      $chars = array_intersect_key(self::$agoChars, $chars);  // set order as in $agoChars.

        if (!$chars) {
          throw new EDateParse("No format passed for AGO[$format].");
        }

      return $chars;
    }

      function NormAgoFmtStr($format) {
        $chars = strtolower( trim($format) );

        $shortcuts = array( 't' => 'smh', '*' => join( array_keys(self::$agoChars) ) );
        $chars = strtr($chars, $shortcuts);
        $chars = $this->DisambiguateAgoM($chars);   // m(i)nute <=> (m) <=> m(o)nth

          // order of pseudochars: e.g. i-d means ihd, not ihwd while i-w means ihwd, not ihw.
          $all = join($this->FixOrderOfPseudoCharsIn( array_keys(self::$agoChars) ));
          while ($pos = strpos($chars, '-')) {
            $rangeStart = $chars[$pos - 1];
            $rangeEnd = $chars[$pos + 1];

              if ($rangeEnd !== null) {
                list($head, $range) = explode($rangeStart, $all, 2);
                if (isset($range)) {
                  list($range, $tail) = explode($rangeEnd, $range, 2);
                  isset($tail) or $range = null;
                }
              }

            if (isset($range)) {
              $chars = substr($chars, 0, $pos) .$range. substr($chars, $pos + 1);
              $range = null;
            } else {
              break;
            }
          }

        return $chars;
      }

        function DisambiguateAgoM($chars) {
          $parts = explode('m', $chars, 3);

            if (isset($parts[2])) {         // ..m..m..
              $parts[0] = strtr($parts[0], 'm', 'i').'i';
              $parts[1] .= 'o';
              $parts[2] = strtr($parts[2], 'm', 'o');
            } elseif (isset($parts[1])) {  // ..m..
              // bM dM wM   My
              if (strpbrk(substr($parts[0], -1), 'bdw') !== false or
                  substr($parts[1], 0, 1) === 'y') {
                $parts[0] .= 'o';
              } else {
                $parts[0] .= 'i';
              }
            }

          return join($parts);
        }

        function FixOrderOfPseudoCharsIn($chars) {
          $result = array();

          $pseudoChar = null;
          foreach ($chars as $char) {
            if (is_array( self::$agoChars[$char] )) {
              $pseudoChar = $char;
            } else {
              $result[] = $char;
              if ($pseudoChar !== null) {
                $result[] = $pseudoChar;
                $pseudoChar = null;
              }
            }
          }

          return $result;
        }

    // $options: ifFAR, ifRange, ifFormat, wordForm.
    function FormatIF($chars, $distance, $options) {
      extract($options, EXTR_SKIP);

      list($char, $distance) = $this->FindNearestLimitedTo($chars, $distance);
      if (!empty($ifFAR)) {
        $ifRange = self::$agoChars[$char];
        is_array($ifRange) and $ifRange = $ifRange[1];
      }

      $allChars = array_keys($chars);
      if (count($allChars) >= 2 and is_array(self::$agoChars[ $allChars[count($allChars) - 2] ])) {
        $last = array_pop($allChars);
        $pseudoChar = array_pop($allChars);
        array_push($allChars, $last, $pseudoChar);
      }

      if (($ifRange = trim($ifRange)) !== '' and array_pop($allChars) === $char) {
        if (!is_numeric($ifRange)) {
          if (empty($ifFAR)) {
            throw new EDateParse("IF>n[] where n must be numberic and non-zero ($ifRange given).");
          } else {
            throw new EDateParse("IF-FAR[] - FAR cannot be used with '$char'.");
          }
        } elseif ($distance > $ifRange) {
          return $this->FormatNormal($ifFormat, $wordForm);
        }
      }
    }

    function FindNearestLimitedTo($ranges, $distance) {
      $nearChar = null;
      $nearDist = $remainder = $distance;
      $enableWeeks = false;

      foreach (self::$agoChars as $char => $length) {
        if (!is_array($length)) {
          $enableWeeks |= $char === 'd';

          if (isset($ranges[$char])) {
            $nearChar = $char;
            $nearDist = $remainder;
          }

          if ($remainder >= $length and $length) {
            $remainder /= $length;
          } else {
            break;
          }
        }
      }

      if ($nearChar) {
        if ($enableWeeks and isset($ranges['w']) and !empty(self::$agoChars['w'])) {
          $length = self::$agoChars['w'][0];
          if (round($nearDist) >= $length) {
            $nearChar = 'w';
            $nearDist /= $length;
          }
        }

        return array($nearChar, $nearDist);
      } else {    // e.g. AGO[m] with distance of 3 sec.
        $ranges = array_keys($ranges);
        return $this->FindNearestTo($distance, $ranges[0]);
      }
    }

      function FindNearestTo($distance, $stopByChar = false) {
        $nearChar = array_keys(self::$agoChars);
        $nearChar = $nearChar[0];

        foreach (self::$agoChars as $char => $length) {
          if (($stopByChar !== false or $distance >= $length) and $length) {
            $nearChar = $char;

            if ($stopByChar === $char) {
              is_array($length) and $distance /= $length[0];
              break;
            } else {
              $distance /= $length;
            }
          } else {
            break;
          }
        }

        return array($nearChar, $distance);
      }

  // returns array of ( false (success) | array('expected'=>, 'got'=>, 'format'=>, 'timestamp'=>, 'lang'=>) )
  // you can use array_filter() to remove succeeded tests.
  static function RunSelfTests() {
    date_default_timezone_set('UTC');
    mb_internal_encoding('UTF-8');

    $result = array();

    $now = time();
    foreach (self::$selfTests as $key => $test) {
      if ($key === 'now') {
        $now = $test;
      } else {
        $lang = $test[0];
        if (isset( self::$languages[$lang] )) {
          array_shift($test);
        } else {
          $lang = 'en';
        }

        list($format, $date, $expected) = $test;
        $timestamp = is_string($date) ? $date : $now + $date;

        $datefmt = new self($timestamp);
        $datefmt->now = $now;
        $datefmt->LoadLanguage($lang);
        $got = $datefmt->FormatAs($format);

        if ($got === $expected) {
          $result[] = false;
        } else {
          $result[] = compact('expected', 'got', 'format', 'timestamp', 'lang');
        }
      }
    }

    return $result;
  }
}

DateFmt::$languages = array(
  'en' => array(
    'name' => 'English',

    // True if number ending rule repets after 100th, e.g.:
    // [1 замоК], 11 замкОВ, [21 замоК, 31 замоК, ...]
    // False if they stay the same (21th as 11th): 1 letter, [11 letterS, 21 letterS]
    'number rolls' => false, 'force AT-form for AGO' => false, '12 hour time' => true,

    'float delim' => '.', 'at-time' => ' at', 'AM' => 'AM', 'PM' => 'PM',

    // d##m & d#my
    'date without year' => 'd/m', 'date with year' => 'd/m/y',
    // h##m & h##ms
    'time without seconds' => 'h:m %', 'time with seconds' => 'h:m:s %',

    'full months'   => array('January', 'February', 'March', 'April', 'May',
                             'June', 'July', 'August', 'September', 'October',
                             'November', 'December'),
    'short months'  => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul',
                             'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),

    'full days'     => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Friday',
                             'Thursday', 'Saturday'),
    'short days'    => array('Sun', 'Mon', 'Tue', 'Wed', 'Fri', 'Thu', 'Sat'),

    'full year' => 'year',
    'short year' => 'y.',

    // 12s, 3 min, 2 years, ...
    // number string: array('stem', 0, 1, 2-4, 5-20)
    'AGO s' => array(' second', 's', '', 's', 's'), 'AGO short s' => 's',
    'AGO i' => array(' minute', 's', '', 's', 's'), 'AGO short i' => 'min',
    'AGO h' => array(' hour', 's', '', 's', 's'),   'AGO short h' => 'h',
    'AGO d' => array(' day', 's', '', 's', 's'),    'AGO short d' => 'd',
    'AGO o' => array(' month', 's', '', 's', 's'),  'AGO short o' => 'mon',
    'AGO w' => array(' week', 's', '', 's', 's'),   'AGO short w' => 'w',
    'AGO y' => array(' year', 's', '', 's', 's'),   'AGO short y' => 'y',

    // number - distance in days.
    'AGO b0' => 'today', 'AGO short b0' => 'today',
    'AGO b1' => 'yesterday', 'AGO short b1' => 'yesterday',
    'AGO b2' => 'day before yesterday', 'AGO short b2' => 'day before',

    'AGO future b0' => 'later today', 'AGO future short b0' => 'today',
    'AGO future b1' => 'tomorrow', 'AGO future short b1' => 'tomorrow',
    'AGO future b2' => 'day after tomorrow', 'AGO future short b2' => 'day after',

    'half s' => 'half a second', 'half i' => 'half a minute', 'half h' => 'half an hour',
    'half d' => 'half a day',    'half o' => 'half a month',  'half w' => 'half a week',
    'half y' => 'half a year',

    'number word' => 'n%', 'exact' => 'n% p',
    'ago' => '% ago', 'in future' => 'after %',
  ),

  'ru' => array(
    'name' => 'Русский',

    'number rolls' => true,
    'force AT-form for AGO' => true,  // было бы "1 минута спустя" вместо "1 минуту спустя".
    '12 hour time' => false,

    'float delim' => ',', 'at-time' => ' в', 'AM' => 'дня', 'PM' => 'вечера',

    'time without seconds' => 'h:m', 'time with seconds' => 'h:m:s',
    'date without year' => 'd.m', 'date with year' => 'd.m.y',

    'full months'   => array('Январь', 'Февраль', 'Март', 'Апрель', 'Май',
                             'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь',
                             'Ноябрь', 'Декабрь'),
    'full months-at'=> array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая',
                             'Июня', 'Июля', 'Августа', 'Сентября', 'Октября',
                             'Ноября', 'Декабря'),
    'short months'  => array('Янд', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл',
                             'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'),

    'full days'     => array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг',
                             'Пятница', 'Суббота'),
    'full days-at'  => array('Воскресенье', 'Понедельник', 'Вторник', 'Среду', 'Четверг',
                             'Пятницу', 'Субботу'),
    'short days'    => array('Вос', 'Пон', 'Вто', 'Сре', 'Чет', 'Пят', 'Суб'),

    'full year' => 'год',
    'full year-at' => 'года',
    'short year' => 'г.',

    // number string: array('stem', 0, 1, 2-4, 5-20)
    'AGO s' => array(' секунд', '', 'а', 'ы', ''),     'AGO short s' => 'с.',
    'AGO i' => array(' минут', '', 'а', 'ы', ''),      'AGO short i' => 'мин',
    'AGO h' => array(' час', 'ов', '', 'а', 'ов'),     'AGO short h' => 'ч',
    'AGO d' => array(' ', 'дней', 'день', 'дня', 'дней'), 'AGO short d' => 'дн',
    'AGO o' => array(' месяц', 'ев', '', 'а', 'ев'),   'AGO short o' => 'мес',
    'AGO w' => array(' недел', 'ь', 'я', 'и', 'ь'),    'AGO short w' => 'н',
    'AGO y' => array(' ', 'лет', 'год', 'года', 'лет'),
      'AGO short y' => array('', 'л', 'г', 'г', 'л'),

      'AGO s-at' => array(' секунд', '', 'у', 'ы', ''),
      'AGO i-at' => array(' минут', '', 'у', 'ы', ''),
      'AGO w-at' => array(' недел', 'ь', 'ю', 'и', 'ь'),

    'AGO b0' => 'сегодня',   'AGO short b0' => 'сег.',
    'AGO b1' => 'вчера',     'AGO short b1' => 'вчера',
    'AGO b2' => 'позавчера', 'AGO short b2' => 'позавч.',

    'AGO future b0' => 'позже сегодня', 'AGO future short b0' => 'сегодня',
    'AGO future b1' => 'завтра', 'AGO future short b1' => 'завтра',
    'AGO future b2' => 'послезавтра', 'AGO future short b2' => 'послез.',

    'half s' => 'полсекунды', 'half i' => 'полминуты', 'half h' => 'полчаса',
    'half d' => 'полдня',     'half o' => 'полмесяца', 'half w' => 'полнедели',
    'half y' => 'полгода',

    '1.5 s' => 'полторы секунды', '1.5 i' => 'полторы минуты', '1.5 h' => 'полтора часа',
    '1.5 d' => 'полтора дня',     '1.5 o' => 'полтора месяца', '1.5 w' => 'полторы недели',
    '1.5 y' => 'полтора года',

    'ago' => '% назад', 'in future' => '% спустя'
  )
);

DateFmt::$selfTests = array(
  'now' => 1309519239,

  array('AGO[h-w]', 5, 'after 0 hours'),
  array('AGO[h-w]', 400, 'after 0.1 hours'),

  array('AGO[h-w]', 1*24*3600, 'after 1 day'),
  array('AGO[h-w]', 6*24*3600, 'after 6 days'),
  array('AGO[h-w]', 7*24*3600, 'after 1 week'),
  array('AGO[h-w]', 18*24*3600, 'after 3 weeks'),   // 3 (not 2) because 18 is closer to 21 than to 14 days.
  array('AGO[h-w]', 99*24*3600, 'after 14 weeks'),
  array('AGO[h-d]', 99*24*3600, 'after 99 days'),
  array('AGO[h-o]', 99*24*3600, 'after 3 months'),

  array('AGO[w]IF>2[on d##my]', 0.5*24*3600, 'after 0 weeks'),
  array('AGO[w]IF>2[on d##my]', 5*24*3600, 'after half a week'),
  array('AGO[w]IF>2[on d##my]', 13*24*3600, 'after 2 weeks'),
  array('AGO[w]IF>2[on d##my]', 14*24*3600, 'after 2 weeks'),
  array('AGO[w]IF>2[on d##my]', 15*24*3600, 'on 16/07/2011'),
  array('AGO[w]IF-FAR[on d##my]', 40*24*3600, 'on 10/08/2011'),

  array('AGO[h-d]IF-FAR[on d##my]', 29*24*3600, 'after 29 days'),
  array('AGO[h-d]IF-FAR[on d##my]', 30*24*3600, 'after 30 days'),
  array('AGO[h-d]IF-FAR[on d##my]', 31*24*3600, 'on 01/08/2011'),
  array('AGO[h-w]IF-FAR[on d##my]', 35*24*3600, 'on 05/08/2011'),
  array('AGO[h-w]IF-FAR[on d##my]', 29*24*3600, 'on 30/07/2011'),
  array('AGO[h-w]IF-FAR[on d##my]', 28*24*3600, 'after 4 weeks'),
  array('AGO[h-w]IF-FAR[on d##my]', 27*24*3600, 'after 4 weeks'),

  array('AGO[i]',   30, 'after half a minute'),
  array('AGO[i]',   -59, 'half a minute ago'),
  array('AGO[i]',   -60, '1 minute ago'),
  array('AGO[w.o]', 1*30*24*3600 + 2*7*24*3600, 'after 2 weeks 1 month'),
  array('AGO[w.o]', 2*30*24*3600 + 2*7*24*3600, 'after 2 weeks 2 months'),
  array('AGO[w]',   2*30*24*3600 + 2*7*24*3600, 'after 11 weeks'),
  array('AGO[w]',   3*24*3600, 'after 0.4 weeks'),
  array('AGO[w]',   5*24*3600, 'after half a week'),

  array('AGO[d.s.i]', 2*24*3600 + 3600 + 3, 'after 2 days 3 seconds 60 minutes'),
  array('AGO-SHORT[h.d.i.s]', 2*24*3600 + 3600 + 3, 'after 1h 2d 3s'),

  array('AGO-SHORT[h.d.i.s]', 0, '0s ago'),
  array('AGO-SHORT[h.d.i.s]', 1, 'after 1s'),
  array('AGO-SHORT[h.d.i.s]', -1, '1s ago'),
  array('AGO-SHORT[h.d.i.s]', 3600, 'after 1h'),
  array('AGO-SHORT[h.d.i.s]', -3600, '1h ago'),

  array('Now is D__, AT[d# of M__ y##] (h##ms).', 0, 'Now is Thursday, 1 of July 2011 (11:20:39 AM).'),
  array('ru', 'Сегодня D__, AT[d# M__ y##] (h##ms).', 0, 'Сегодня Пятница, 1 Июля 2011 (11:20:39).'),
  array('Last commit was AGO[d.h].', -40*24*3600 - 14*3600 - 12, 'Last commit was 40 days 14 hours ago.'),
  array('ru', 'Last commit was AGO[d.h].', -40*24*3600 - 14*3600 - 12, 'Last commit was 40 дней 14 часов назад.'),
  array('It was on H#:m## A.M..', '1309519239', 'It was on 11:20 AM.'),
  array('ru', 'Это было в H#:m## A.M..', '1309519239', 'Это было в 11:20 дня.'),
  array('This entry was posted AGO[*]AT D__, d# M__ y##.', -50*60 - 3, 'This entry was posted 50 minutes ago at Thursday, 1 July 2011.'),
  array('This document was created on d#my.', '1294790400', 'This document was created on 12/01/2011.'),
  array('ru', 'This document was created on d#my.', '1294790400', 'This document was created on 12.01.2011.'),
  array('A diary post saying AT[d# m__ y##] in its top right corner.', '1355270400', 'A diary post saying 12 december 2012 in its top right corner.'),
  array('ru', 'Записка с надписью AT[d# m__ y##] в правом верхнем углу.', '1355270400', 'Записка с надписью 12 декабря 2012 в правом верхнем углу.'),
  array('ru', 'Записка с надписью d# m__ y## в правом верхнем углу.', '1355270400', 'Записка с надписью 12 декабрь 2012 в правом верхнем углу.'),
  array('This reply was posted AGO[s-d]IF-FAR[on d#my]AT D__.', 3*3600, 'This reply was posted after 3 hours at Thursday.'),
  array('This reply was posted AGO[s-d]IF-FAR[on d#my]AT D__.', 40*24*3600, 'This reply was posted on 10/08/2011 at Wednesday.'),
  array('Posted at d##-M_-y# h##m (AGO[h-y]_ since last post...', 0.5*3600, 'Posted at 01-Jul-11 11:50 AM (half an hour since last post...'),
  array('...and AGO[*]_ before next reply).', -5*60, '...and 5 minutes before next reply).'),
  array('ru', 'Добавлено d##-M_-y# h##m (через AGO[h-y]_ после предыдущего сообщения...', 0.5*3600, 'Добавлено 01-Июл-11 11:50 (через полчаса после предыдущего сообщения...'),
  array('ru', '...и за AGO[*]_ перед следующим).', -5*60, '...и за 5 минут перед следующим).')
);

//var_dump(array_filter(DateFmt::RunSelfTests()));
