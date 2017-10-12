<?php  function lcr57fdc818c7146v($cx, $in, $base, $path, $args = null) {
  $count = count($cx['scopes']);
  while ($base) {
   $v = $base;
   foreach ($path as $name) {
    if (is_array($v) && isset($v[$name])) {
     $v = $v[$name];
     continue;
    }
    if (is_object($v)) {
     if ($cx['flags']['prop'] && !($v instanceof \Closure) && isset($v->$name)) {
      $v = $v->$name;
      continue;
     }
     if ($cx['flags']['method'] && is_callable(array($v, $name))) {
      $v = $v->$name();
      continue;
     }
    }
    if ($cx['flags']['mustlok']) {
     unset($v);
     break;
    }
    return null;
   }
   if (isset($v)) {
    if ($v instanceof \Closure) {
     if ($cx['flags']['mustlam'] || $cx['flags']['lambda']) {
      if (!$cx['flags']['knohlp'] && ($args || ($args === 0))) {
       $A = $args ? $args[0] : array();
       $A[] = array('hash' => $args[1], '_this' => $in);
      } else {
       $A = array($in);
      }
      $v = call_user_func_array($v, $A);
     }
    }
    return $v;
   }
   $count--;
   switch ($count) {
    case -1:
     $base = $cx['sp_vars']['root'];
     break;
    case -2:
     return null;
    default:
     $base = $cx['scopes'][$count];
   }
  }
  if ($args) {
   lcr57fdc818c7146err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57fdc818c7146encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57fdc818c7146raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57fdc818c7146hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
  $isBlock = (is_object($cb) && ($cb instanceof \Closure));

  if (isset($cx['blparam'][0][$ch])) {
   return $cx['blparam'][0][$ch];
  }

  $args = $vars[0];
  $options = array(
   'name' => $ch,
   'hash' => $vars[1],
   'contexts' => count($cx['scopes']) ? $cx['scopes'] : array(null),
   'fn.blockParams' => 0,
  );

  if ($isBlock) {
   $options['_this'] = &$op;
  } else {
   $options['_this'] = &$inverted;
  }

  if (isset($vars[2])) {
   $options['fn.blockParams'] = count($vars[2]);
  }

  // $invert the logic
  if ($inverted) {
   $tmp = $else;
   $else = $cb;
   $cb = $tmp;
  }

  if ($isBlock) {
   $options['fn'] = function ($context = '_NO_INPUT_HERE_', $data = null) use ($cx, &$op, $cb, $options, $vars) {
    if ($cx['flags']['echo']) {
     ob_start();
    }
    if (isset($data['data'])) {
     $old_spvar = $cx['sp_vars'];
     $cx['sp_vars'] = array_merge(array('root' => $old_spvar['root']), $data['data'], array('_parent' => $old_spvar));
    }
    $ex = false;
    if (isset($data['blockParams']) && isset($vars[2])) {
     $ex = array_combine($vars[2], array_slice($data['blockParams'], 0, count($vars[2])));
     array_unshift($cx['blparam'], $ex);
    } else if (isset($cx['blparam'][0])) {
     $ex = $cx['blparam'][0];
    }
    if (($context === '_NO_INPUT_HERE_') || ($context === $op)) {
     $ret = $cb($cx, is_array($ex) ? lcr57fdc818c7146m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57fdc818c7146m($cx, $context, $ex) : $context);
     array_pop($cx['scopes']);
    }
    if (isset($data['data'])) {
     $cx['sp_vars'] = $old_spvar;
    }
    return $cx['flags']['echo'] ? ob_get_clean() : $ret;
   };
  }

  if ($else) {
   $options['inverse'] = function ($context = '_NO_INPUT_HERE_') use ($cx, $op, $else) {
    if ($cx['flags']['echo']) {
     ob_start();
    }
    if ($context === '_NO_INPUT_HERE_') {
     $ret = $else($cx, $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $else($cx, $context);
     array_pop($cx['scopes']);
    }
    return $cx['flags']['echo'] ? ob_get_clean() : $ret;
   };
  } else {
   $options['inverse'] = function () {
    return '';
   };
  }

  if ($cx['flags']['spvar']) {
   $options['data'] = $cx['sp_vars'];
  }

  $args[] = $options;
  $e = null;
  $r = true;

  try {
   $r = call_user_func_array($cx['helpers'][$ch], $args);
  } catch (\Exception $E) {
   $e = "Runtime: call custom helper '$ch' error: " . $E->getMessage();
  }

  if($e !== null) {
   lcr57fdc818c7146err($cx, $e);
  }

  return $r;
 }
 function lcr57fdc818c7146ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57fdc818c7146sec($cx, $v, $bp, $in, $each, $cb, $else = null) {
  $push = ($in !== $v) || $each;

  $isAry = is_array($v) || ($v instanceof \ArrayObject);
  $isTrav = $v instanceof \Traversable;
  $loop = $each;
  $keys = null;
  $last = null;
  $isObj = false;

  if ($isAry && $else !== null && count($v) === 0) {
   $ret = $else($cx, $in);
   return $ret;
  }

  // #var, detect input type is object or not
  if (!$loop && $isAry) {
   $keys = array_keys($v);
   $loop = (count(array_diff_key($v, array_keys($keys))) == 0);
   $isObj = !$loop;
  }

  if (($loop && $isAry) || $isTrav) {
   if ($each && !$isTrav) {
    // Detect input type is object or not when never done once
    if ($keys == null) {
     $keys = array_keys($v);
     $isObj = (count(array_diff_key($v, array_keys($keys))) > 0);
    }
   }
   $ret = array();
   if ($push) {
    $cx['scopes'][] = $in;
   }
   $i = 0;
   if ($cx['flags']['spvar']) {
    $old_spvar = $cx['sp_vars'];
    $cx['sp_vars'] = array_merge(array('root' => $old_spvar['root']), $old_spvar, array('_parent' => $old_spvar));
    if (!$isTrav) {
     $last = count($keys) - 1;
    }
   }

   $isSparceArray = $isObj && (count(array_filter(array_keys($v), 'is_string')) == 0);
   foreach ($v as $index => $raw) {
    if ($cx['flags']['spvar']) {
     $cx['sp_vars']['first'] = ($i === 0);
     $cx['sp_vars']['last'] = ($i == $last);
     $cx['sp_vars']['key'] = $index;
     $cx['sp_vars']['index'] = $isSparceArray ? $index : $i;
     $i++;
    }
    if (isset($bp[0])) {
     $raw = lcr57fdc818c7146m($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr57fdc818c7146m($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
    }
    $ret[] = $cb($cx, $raw);
   }
   if ($cx['flags']['spvar']) {
    if ($isObj) {
     unset($cx['sp_vars']['key']);
    } else {
     unset($cx['sp_vars']['last']);
    }
    unset($cx['sp_vars']['index']);
    unset($cx['sp_vars']['first']);
    $cx['sp_vars'] = $old_spvar;
   }
   if ($push) {
    array_pop($cx['scopes']);
   }
   return join('', $ret);
  }
  if ($each) {
   if ($else !== null) {
    $ret = $else($cx, $v);
    return $ret;
   }
   return '';
  }
  if ($isAry) {
   if ($push) {
    $cx['scopes'][] = $in;
   }
   $ret = $cb($cx, $v);
   if ($push) {
    array_pop($cx['scopes']);
   }
   return $ret;
  }

  if ($v === true) {
   return $cb($cx, $in);
  }

  if (($v !== null) && ($v !== false)) {
   return $cb($cx, $v);
  }

  if ($else !== null) {
   $ret = $else($cx, $in);
   return $ret;
  }

  return '';
 }
 function lcr57fdc818c7146p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57fdc818c7146err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57fdc818c7146m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57fdc818c7146err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57fdc818c7146raw($cx, $v) {
  if ($v === true) {
   if ($cx['flags']['jstrue']) {
    return 'true';
   }
  }

  if (($v === false)) {
   if ($cx['flags']['jstrue']) {
    return 'false';
   }
  }

  if (is_array($v)) {
   if ($cx['flags']['jsobj']) {
    if (count(array_diff_key($v, array_keys(array_keys($v)))) > 0) {
     return '[object Object]';
    } else {
     $ret = array();
     foreach ($v as $k => $vv) {
      $ret[] = lcr57fdc818c7146raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr57fdc818c7146m($cx, $a, $b) {
  if (is_array($b)) {
   if ($a === null) {
    return $b;
   } else if (is_array($a)) {
    return array_merge($a, $b);
   } else if (($cx['flags']['method'] || $cx['flags']['prop']) && is_object($a)) {
    foreach ($b as $i => $v) {
     $a->$i = $v;
    }
   }
  }
  return $a;
 }
if (!class_exists("LS")) {
class LS {
    public function __construct($str, $escape = false) {
        $this->string = $escape ? (($escape === 'encq') ? static::encq(static::$jsContext, $str) : static::enc(static::$jsContext, $str)) : $str;
    }
    public function __toString() {
        return $this->string;
    }
    public static function escapeTemplate($template) {
        return addcslashes(addcslashes($template, '\\'), "'");
    }
    public static function raw($cx, $v) {
        if ($v === true) {
            if ($cx['flags']['jstrue']) {
                return 'true';
            }
        }

        if (($v === false)) {
            if ($cx['flags']['jstrue']) {
                return 'false';
            }
        }

        if (is_array($v)) {
            if ($cx['flags']['jsobj']) {
                if (count(array_diff_key($v, array_keys(array_keys($v)))) > 0) {
                    return '[object Object]';
                } else {
                    $ret = array();
                    foreach ($v as $k => $vv) {
                        $ret[] = static::raw($cx, $vv);
                    }
                    return join(',', $ret);
                }
            } else {
                return 'Array';
            }
        }

        return "$v";
    }
    public static function enc($cx, $var) {
        return htmlentities(static::raw($cx, $var), ENT_QUOTES, 'UTF-8');
    }
    public static function encq($cx, $var) {
        return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(static::raw($cx, $var), ENT_QUOTES, 'UTF-8'));
    }
}
}
return function ($in = null, $options = null) {
    $helpers = array(            'admin_menu' => function() {
        $args = func_get_args();
        $options = end($args);
        $items = \Postleaf\Admin::getMenuItems();

        // Generate `current` value for each item
        foreach($items as $key => $value) {
            $items[$key]['current'] = \Postleaf\Postleaf::isCurrentUrl($value['link']);
        }

        if(count($items)) {
            return $options['fn']([
                'items' => $items
            ]);
        } else {
            // No items, do {{else}}
            return $options['inverse'] ? $options['inverse']() : '';
        }
    },
            'admin_scripts' => function() {
        $args = func_get_args();
        $options = end($args);
        $html = '';

        foreach((array) $options['_this']['scripts'] as $script) {
            // If this is a fully qualified URL, return is as-is
            if(preg_match('/^(http:|https:|mailto:|\/\/:)/i', $script)) {
                $src = $script;
            } else {
                $src =
                    self::url('source/assets/js', $script) . '?v=' .
                    $options['data']['postleaf']['version'];
            }
            $html .= '<script src="' . htmlspecialchars($src) . '"></script>';
        }

        return new LS($html);
    },
            'admin_styles' => function() {
        $args = func_get_args();
        $options = end($args);
        $html = '';

        foreach((array) $options['_this']['styles'] as $style) {
            $href =
                self::url('source/assets/css', $style) . '?v=' .
                $options['data']['postleaf']['version'];
            $html .= '<link rel="stylesheet" href="' . htmlspecialchars($href) . '">';
        }

        return new LS($html);
    },
            'admin_url' => function($path, $options = null) {
        if(!$options) {
            $options = $path;
            $path = null;
        }

        // Add hash attributes as query string data
        if(is_array($options) && count($options['hash'])) {
            $path = rtrim($path, '/') . '/?' . http_build_query($options['hash'], null, '&', PHP_QUERY_RFC3986);
        }

        return \Postleaf\Admin::url($path);
    },
            'blog_url' => function($path, $options = null) {
        return \Postleaf\Blog::url($options['hash']['page']);
    },
            'url' => function($path, $options = null) {
        if(!$options) {
            $options = $path;
            $path = '';
        }

        // If this is a fully qualified URL, return is as-is
        if(preg_match('/^(http:|https:|mailto:|\/\/:)/i', $path)) {
            return $path;
        } else {
            return \Postleaf\Postleaf::url($path);
        }
    },
            'date' => function($date, $options = null) {
        // If only one argument was passed in, adjust options and set the default $date
        if(!$options) {
            $options = $date;

            // Try this.date
            if(isset($options['_this']['pub_date'])) {
                $date = $options['_this']['pub_date'];
            } else {
                // Fallback to the current date/time
                $date = date('Y-m-d H:i:s');
            }
        }

        // Determine format
        $format = isset($options['hash']['format']) ? $options['hash']['format'] : '%Y-%m-%d';

        return \Postleaf\Postleaf::strftime($format, strtotime($date));
    },
            'either' => function() {
        $args = func_get_args();
        $options = end($args);

        // Loop through each argument and look for a truthy value
        for($i = 0; $i < count($args) - 1; $i++) {
            if($args[$i]) {
                return $options['fn']($args[$i]);
            }
        }

        // Do {{else}} if there aren't any truthy values
        return $options['inverse'] ? $options['inverse']() : '';
    },
            'is' => function() {
        $args = func_get_args();
        $options = end($args);

        switch(count($args) - 1) {
            // One variable
            case 1:
                $left = true;
                $right = !!$args[0];
                $operator = '==';
                break;

            // Two variables
            case 2:
                $left = $args[0];
                $operator = '==';
                $right = $args[1];
                break;

            // Two variables + operator
            case 3:
                $left = $args[0];
                $operator = $args[1];
                $right = $args[2];
        }

        // Compare values
        switch(strtolower($operator)) {
            case '>':
                $is = $left > $right;
                break;
            case '>=':
                $is = $left >= $right;
                break;
            case '<':
                $is = $left < $right;
                break;
            case '<=':
                $is = $left <= $right;
                break;
            case '===':
                $is = $left === $right;
                break;
            case '&&':
            case 'and':
                $is = $left && $right;
                break;
            case '||':
            case 'or':
                $is = ($left || $right);
                break;
            case 'xor':
                $is = ($left xor $right);
                break;
            case '!=':
            case 'not':
                $is = $left != $right;
                break;
            case '!==':
                $is = $left !== $right;
                break;
            case 'in':
            case 'not in':
                $is = true;
                if(!is_array($right)) {
                    // Split CSV into an array
                    $right = explode(',', (string) $right);
                    $right = array_map(function($a) {
                        return trim($a);
                    }, $right);
                }
                $is = in_array($left, $right);
                if(strtolower($operator) === 'not in') $is = !$is;
                break;
            case 'type':
                if($right === 'array') $is = is_array($left);
                if($right === 'string') $is = is_string($left);
                break;
            default:
                $is = $left == $right;
                break;
        }

        if($is) {
            return $options['fn']();
        } else {
            return $options['inverse'] ? $options['inverse']() : '';
        }
    },
            'L' => function($term, $options) {
        return \Postleaf\Language::term($term, $options['hash']);
    },
            'math' => function() {
        $args = func_get_args();
        $options = end($args);

        switch(count($args) - 1) {
            // One argument (number)
            case 1:
                return $args[0];

            // Two arguments (operator, number)
            case 2:
                switch($args[0]) {
                    case 'abs':
                        return abs($args[1]);
                    case 'ceil':
                        return ceil($args[1]);
                    case 'floor':
                        return floor($args[1]);
                    case 'round':
                        return round($args[1]);
                    case 'sqrt':
                        return sqrt($args[1]);
                }
                break;

            // Three arguments (number, operator, number)
            case 3:
                switch($args[1]) {
                    case '+':
                        return $args[0] + $args[2];
                    case '-':
                        return $args[0] - $args[2];
                    case '*':
                        return $args[0] * $args[2];
                    case '/':
                        return $args[2] === 0 ? '' : $args[0] / $args[2];
                    case '^':
                        return pow($args[0], $args[2]);
                    case '%':
                        return $args[0] % $args[2];
                }
                break;
        }
    },
            'number' => function($number, $options) {
        $places = isset($options['hash']['places']) ?
            $options['hash']['places'] : 0;

        $decimal = isset($options['hash']['decimal']) ?
            $options['hash']['decimal'] : '.';

        $thousands = isset($options['hash']['thousands']) ?
            $options['hash']['thousands'] : ',';

        return number_format($number, $places, $decimal, $thousands);
    },
);
    $partials = array('header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('title'))),' &middot; ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</title>
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'    <meta name="apple-mobile-web-app-title" content="Postleaf">
',$sp,'    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1, minimal-ui">
',$sp,'    <meta name="postleaf:language" data-cancel="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'" data-ok="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('ok'),array()), 'encq', $in)),'" data-changes-saved="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('your_changes_have_been_saved'),array()), 'encq', $in)),'">
',$sp,'    <meta name="postleaf:template" content="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template'))),'">
',$sp,'    <meta name="postleaf:url" data-base="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" data-admin="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'admin_url', array(array(),array()), 'encq', $in)),'">
',$sp,'',lcr57fdc818c7146hbch($cx, 'either', array(array(lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')),'source/assets/img/logo-color.png'),array()), $in, false, function($cx, $in)use($sp){echo '    <link rel="apple-touch-icon" href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'    <link rel="shortcut icon" href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'';}),'    <link rel="stylesheet" href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array('source/assets/css/lib.css'),array()), 'encq', $in)),'?v=',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'">
',$sp,'    ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'admin_styles', array(array(),array()), 'encq', $in)),'
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,600,600italic">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono:300">
',$sp,'</head>
',$sp,'<body class="admin';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('body_class')), false)){echo ' ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('body_class'))),'';}else{echo '';}echo ' preload">';return ob_get_clean();},
'menu' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<nav class="main-menu">
',$sp,'    <a aria-label="Postleaf" href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" title="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'" data-toggle="tooltip">
',$sp,'        <img src="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array('source/assets/img/1.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'    </a>
',$sp,'
',$sp,'',lcr57fdc818c7146hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '            <a href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('link'))),'" title="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('title'))),'" data-toggle="tooltip" class="';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                    <img src="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                    <i class="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '            </a>
',$sp,'';}),'';}),'</nav>
',$sp,'
',$sp,'<nav class="mobile-menu">
',$sp,'    <div class="mobile-menu-header">
',$sp,'        <a class="mobile-menu-logo" href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'blog_url', array(array(),array()), 'encq', $in)),'">
',$sp,'            <img src="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array('source/assets/img/12.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'        </a>
',$sp,'        <span class="mobile-menu-toggle" href="#">
',$sp,'            <i class="fa fa-navicon"></i>
',$sp,'        </span>
',$sp,'        <div class="mobile-menu-title">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('title'))),'</div>
',$sp,'    </div>
',$sp,'    <div class="mobile-menu-items">
',$sp,'',lcr57fdc818c7146hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a href="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('link'))),'" class="';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                        <img src="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                        <i class="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '                    <span class="description">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('title'))),'</span>
',$sp,'                </a>
',$sp,'';}),'';}),'    </div>
',$sp,'</nav>';return ob_get_clean();},
'backups-table' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('backups')), false)){echo '    <table class="table backups-table">
',$sp,'',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('backups')), null, $in, true, function($cx, $in)use($sp){echo '            <tr>
',$sp,'                <td>
',$sp,'                    ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('filename'))),'<br>
',$sp,'                    <small class="text-muted">
',$sp,'                        ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'date', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('date'))),array('format'=>'%d %b %Y')), 'encq', $in)),' &middot;
',$sp,'                        ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'number', array(array(lcr57fdc818c7146hbch($cx, 'math', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('size')),'/',1000000),array()), 'raw', $in)),array()), 'encq', $in)),'MB
',$sp,'                    </small>
',$sp,'                </td>
',$sp,'                <td>
',$sp,'                    <button type="button" class="btn btn-sm btn-secondary" data-download-backup="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('filename'))),'">
',$sp,'                        <i class="fa fa-download"></i>
',$sp,'                    </a>
',$sp,'                </td>
',$sp,'                <td>
',$sp,'                    <button
',$sp,'                        type="button"
',$sp,'                        class="btn btn-sm btn-secondary"
',$sp,'                        data-delete-backup="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('filename'))),'"
',$sp,'                        data-confirm="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('delete_this_backup'),array()), 'encq', $in)),'"
',$sp,'                    >
',$sp,'                        <i class="fa fa-trash-o"></i>
',$sp,'                    </button>
',$sp,'                </td>
',$sp,'                <td>
',$sp,'                    <button
',$sp,'                        type="button"
',$sp,'                        class="btn btn-sm btn-warning"
',$sp,'                        data-restore-backup="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('filename'))),'"
',$sp,'                        data-confirm="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('restoring_a_backup_will_overwrite_all_of_your_data...'),array()), 'encq', $in)),'"
',$sp,'                    >
',$sp,'                        <i class="fa fa-undo"></i>
',$sp,'                    </button>
',$sp,'                    <i class="loader loader-sm" hidden></i>
',$sp,'                </td>
',$sp,'            </tr>
',$sp,'';}),'    </table>
',$sp,'';}else{echo '    <div class="backups-table-none valign">
',$sp,'        <div class="valign-middle">
',$sp,'            <i class="fa fa-archive"></i>
',$sp,'            ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('no_backups'),array()), 'encq', $in)),'
',$sp,'        </div>
',$sp,'    </div>
',$sp,'';}echo '';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user')), false)){echo '<div class="locater-overlay" hidden></div>
',$sp,'<div class="locater" hidden>
',$sp,'    <div class="form-group">
',$sp,'        <div class="inner-addon-group">
',$sp,'            <span class="inner-addon"><i class="fa fa-search"></i></span>
',$sp,'            <input type="text" class="form-control locater-input" placeholder="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'">
',$sp,'        </div>
',$sp,'    </div>
',$sp,'    <div class="locater-results"></div>
',$sp,'</div>
',$sp,'';}else{echo '';}echo '
',$sp,'<script src="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array('source/assets/js/lib.min.js'),array()), 'encq', $in)),'?v=',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'"></script>
',$sp,'',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'admin_scripts', array(array(),array()), 'encq', $in)),'
',$sp,'</body>
',$sp,'</html>';return ob_get_clean();});
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'spvar' => true,
            'prop' => true,
            'method' => false,
            'lambda' => false,
            'mustlok' => false,
            'mustlam' => false,
            'echo' => true,
            'partnc' => false,
            'knohlp' => false,
            'debug' => isset($options['debug']) ? $options['debug'] : 1,
        ),
        'constants' =>  array(
            'DEBUG_ERROR_LOG' => 1,
            'DEBUG_ERROR_EXCEPTION' => 2,
            'DEBUG_TAGS' => 4,
            'DEBUG_TAGS_ANSI' => 12,
            'DEBUG_TAGS_HTML' => 20,
        ),
        'helpers' => isset($options['helpers']) ? array_merge($helpers, $options['helpers']) : $helpers,
        'partials' => isset($options['partials']) ? array_merge($partials, $options['partials']) : $partials,
        'scopes' => array(),
        'sp_vars' => isset($options['data']) ? array_merge(array('root' => $in), $options['data']) : array('root' => $in),
        'blparam' => array(),
        'partialid' => 0,
        'runtime' => '\LightnCandy\Runtime',
    );
    
    ob_start();echo '',lcr57fdc818c7146p($cx, 'header', array(array($in),array()),0),'
',lcr57fdc818c7146p($cx, 'menu', array(array($in),array()),0),'
<div class="container-fluid">
    <div class="top-toolbar">
        <div class="top-toolbar-section col-xs-6">
            <h1>
                ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('settings'),array()), 'encq', $in)),'
            </h1>
        </div>
        <div class="top-toolbar-section col-xs-6 text-xs-right">
            <button type="button" class="submit btn btn-primary">
                ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('save'),array()), 'encq', $in)),'
            </button>
        </div>
    </div>
</div>

<div class="main-container stretch-down">
    <form class="settings-form" autocomplete="off">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#general" role="tab" data-toggle="tab">
                    <i class="fa fa-cog hidden-sm-up"></i>
                    <span class="hidden-xs-down">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('general'),array()), 'encq', $in)),'</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#advanced" role="tab" data-toggle="tab">
                    <i class="fa fa-code hidden-sm-up"></i>
                    <span class="hidden-xs-down">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('advanced'),array()), 'encq', $in)),'</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#backups" role="tab" data-toggle="tab">
                    <i class="fa fa-archive hidden-sm-up"></i>
                    <span class="hidden-xs-down">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('backups'),array()), 'encq', $in)),'</span>
                </a>
            </li>
        </ul>

        <div class="row">
            <div class="col-lg-6 push-lg-3 col-md-8 push-md-2 col-sm-10 push-sm-1">
                <div class="tab-content">
                    <div class="tab-pane active" id="general" role="tabpanel">
                        <div class="form-group">
                            <label for="title">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('title'),array()), 'encq', $in)),'</label>
                            <input class="form-control" type="text" name="title" id="title" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'">
                        </div>
                        <div class="form-group">
                            <label for="tagline">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('tagline'),array()), 'encq', $in)),'</label>
                            <input class="form-control" type="text" name="tagline" id="tagline" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','tagline'))),'">
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="homepage">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('custom_homepage'),array()), 'encq', $in)),'</label>
                                    <select class="form-control" name="homepage" id="homepage">
                                        <option value=""',lcr57fdc818c7146hbch($cx, 'is', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('slug')),lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','homepage'))),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('none'),array()), 'encq', $in)),'</option>
',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('pages')), null, $in, true, function($cx, $in) {echo '                                        <option value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('slug'))),'"',lcr57fdc818c7146hbch($cx, 'is', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('slug')),lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','homepage'))),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('title'))),'</option>
';}),'                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="twitter">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('twitter'),array()), 'encq', $in)),'</label>
                                    <div class="inner-addon-group">
                                        <span class="inner-addon"><i class="fa fa-twitter"></i></span>
                                        <input class="form-control" type="text" name="twitter" id="twitter" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','twitter'))),'">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="theme">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('theme'),array()), 'encq', $in)),'</label>
                                    <select class="form-control" name="theme" id="theme">
',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('themes')), null, $in, true, function($cx, $in) {echo '                                        <option value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('dir'))),'"',lcr57fdc818c7146hbch($cx, 'is', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('dir')),lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','theme'))),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('name'))),'</option>
';}),'                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="posts-per-page">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('posts_per_page'),array()), 'encq', $in)),'</label>
                                    <input class="form-control" type="number" name="posts-per-page" id="posts-per-page" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','posts_per_page'))),'">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('cover_photo'),array()), 'encq', $in)),'</label>
                            <input type="hidden" name="cover" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','cover'))),'">
                            <div class="cover card"';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','cover')), false)){echo ' style="background-image: url(\'',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','cover'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '>
                                <div class="controls">
                                    <label class="upload-cover btn btn-translucent-dark">
                                        <i class="fa fa-fw fa-camera"></i>
                                        <input type="file" accept="image/*" style="display: none;">
                                    </label>
                                    <button type="button" class="remove-cover btn btn-translucent-dark"';if (!lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','cover')), false)){echo ' hidden';}else{echo '';}echo '>
                                        <i class="fa fa-fw fa-remove"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('logo'),array()), 'encq', $in)),'</label>
                                <input type="hidden" name="logo" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo'))),'">
                                <div class="logo card card-block"';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo')), false)){echo ' style="background-image: url(\'',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '>
                                    <div class="controls">
                                        <label class="upload-logo btn btn-translucent-dark">
                                            <i class="fa fa-fw fa-camera"></i>
                                            <input type="file" accept="image/*" style="display: none;">
                                        </label>
                                        <button type="button" class="remove-logo btn btn-translucent-dark"';if (!lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo')), false)){echo ' hidden';}else{echo '';}echo '>
                                            <i class="fa fa-fw fa-remove"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('favicon'),array()), 'encq', $in)),'</label>
                                <input type="hidden" name="favicon" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon'))),'">
                                <div class="favicon card card-block"';if (lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')), false)){echo ' style="background-image: url(\'',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'url', array(array(lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '>
                                    <div class="controls">
                                        <label class="upload-favicon btn btn-translucent-dark">
                                            <i class="fa fa-fw fa-camera"></i>
                                            <input type="file" accept="image/*" style="display: none;">
                                        </label>
                                        <button type="button" class="remove-favicon btn btn-translucent-dark"';if (!lcr57fdc818c7146ifvar($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')), false)){echo ' hidden';}else{echo '';}echo '>
                                            <i class="fa fa-fw fa-remove"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="advanced" role="tabpanel">
                        <h3>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('locale'),array()), 'encq', $in)),'</h3>
                        <div class="form-group">
                            <label for="language">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('language'),array()), 'encq', $in)),'</label>
                            <select class="form-control" name="language" id="language">
',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('languages')), null, $in, true, function($cx, $in) {echo '                                <option value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('code'))),'"',lcr57fdc818c7146hbch($cx, 'is', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('code')),lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','language'))),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('name'))),'</option>
';}),'                            </select>
                        </div>
                        <div class="form-group">
                            <label for="timezone">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('time_zone'),array()), 'encq', $in)),'</label>
                            <select class="form-control" name="timezone" id="timezone">
',lcr57fdc818c7146sec($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('timezones')), null, $in, true, function($cx, $in) {echo '                                <option value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('code'))),'"',lcr57fdc818c7146hbch($cx, 'is', array(array(lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('code')),lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','timezone'))),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($in) ? $in : null, array('name'))),'</option>
';}),'                            </select>
                        </div>
                        <h3 class="m-t-3">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('editor'),array()), 'encq', $in)),'</h3>
                        <div class="form-group">
                            <label for="default-title">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('default_title'),array()), 'encq', $in)),'</label>
                            <input class="form-control" type="text" name="default-title" id="default-title" value="',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','default_title'))),'">
                        </div>
                        <div class="form-group">
                            <label for="default-content">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('default_content'),array()), 'encq', $in)),'</label>
                            <div class="tag-cover">
                                <span class="tag tag-default tag-tr tag-outside">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('markdown'),array()), 'encq', $in)),'</span>
                                <textarea class="form-control" name="default-content" id="default-content" rows="6">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','default_content'))),'</textarea>
                            </div>
                        </div>
                        <h3 class="m-t-3">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('custom_code'),array()), 'encq', $in)),'</h3>
                        <div class="form-group">
                            <label for="head-code">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('inject_this_code_into_postleaf_head'),array()), 'encq', $in)),'</label>
                            <div class="tag-cover">
                                <span class="tag tag-default tag-tr tag-outside">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('html'),array()), 'encq', $in)),'</span>
                                <textarea class="form-control code" name="head-code" id="head-code" rows="6" spellcheck="false">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','head_code'))),'</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="foot-code">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('inject_this_code_into_postleaf_foot'),array()), 'encq', $in)),'</label>
                            <div class="tag-cover">
                                <span class="tag tag-default tag-tr tag-outside">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('html'),array()), 'encq', $in)),'</span>
                                <div class="tag-cover">
                                    <span class="tag tag-default tag-tr tag-outside">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('html'),array()), 'encq', $in)),'</span>
                                    <textarea class="form-control code" name="foot-code" id="foot-code" rows="6" spellcheck="false">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','foot_code'))),'</textarea>
                                </div>
                            </div>
                        </div>

                        <h3 class="m-t-3">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('cache'),array()), 'encq', $in)),'</h3>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="hbs-cache" value="on"',lcr57fdc818c7146hbch($cx, 'is', array(array(lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','hbs_cache')),'on'),array()), $in, false, function($cx, $in) {echo ' checked';}),'>
                                    ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('cache_templates_for_faster_rendering'),array()), 'encq', $in)),' ⚡️<br>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button data-clear-cache class="btn btn-secondary" type="button">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('clear_cache'),array()), 'encq', $in)),'</button>
                        </div>

                        <div class="form-group m-t-3 text-xs-center text-muted">
                            Postleaf ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'
                        </div>
                    </div>

                    <div class="tab-pane" id="backups" role="tabpanel">
                        <h3>',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('create_backup'),array()), 'encq', $in)),'</h3>
                        <p>
                            ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('postleaf_can_create_a_backup_of_your_entire_website...'),array()), 'encq', $in)),'
                        </p>
                        <div class="form-group">
                            <button class="btn btn-primary" type="button" data-create-backup>
                                ',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('backup_now'),array()), 'encq', $in)),'
                            </button>
                            <i class="loader loader-md create-backup-loader" hidden></i>
                        </div>
                        <h3 class="m-t-3">',lcr57fdc818c7146encq($cx, lcr57fdc818c7146hbch($cx, 'L', array(array('available_backups'),array()), 'encq', $in)),'</h3>
                        <div class="available-backups m-t-2">
',lcr57fdc818c7146p($cx, 'backups-table', array(array($in),array()),0, '                            '),'                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" hidden></button>
    </form>
</div>

',lcr57fdc818c7146p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};