<?php  function lcr584880f0af486v($cx, $in, $base, $path, $args = null) {
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
   lcr584880f0af486err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr584880f0af486encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr584880f0af486raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr584880f0af486hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr584880f0af486m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr584880f0af486m($cx, $context, $ex) : $context);
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
   lcr584880f0af486err($cx, $e);
  }

  return $r;
 }
 function lcr584880f0af486err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr584880f0af486raw($cx, $v) {
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
      $ret[] = lcr584880f0af486raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr584880f0af486m($cx, $a, $b) {
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
    $helpers = array(            'content' => function($options) {
        $content = $options['_this']['content'];
        $editable = mb_strtolower($options['hash']['editable']) === 'true';

        // Is the post being rendered in the editor?
        if($editable && $options['data']['meta']['editable']) {
            // If so, wrap in editable tags
            //
            // Note that content is also being inserted into the data-postleaf-html attribute inside
            // the div. We do this so we can grab the original markup once it's loaded into the
            // editor, as the code may have been altered by scripts.
            return new LS(
                '<div data-postleaf-id="post:content" data-postleaf-type="post-content" ' .
                'data-postleaf-html="' . htmlspecialchars($content) . '">' .
                $content .
                '</div>'
            );
        } else {
            // Otherwise, just return the raw HTML
            return new LS($content);
        }
    },
            'post' => function($slug, $options = null) {
        if(!$options) {
            $options = $slug;

            if(is_array($options['_this']['post'])) {
                // Try this.post as array
                $post = $options['_this']['post'];
            }
        } else {
            // Get the post by slug
            $post = \Postleaf\Post::get($slug);
        }

        // Do {{else}} if no post is found
        if(!$post) {
            return $options['inverse'] ? $options['inverse']() : '';
        }

        return $options['fn']($post);
    },
            'postleaf_foot' => function($options) {
        $html = '';

        // If we're editing a post, add required code
        if($options['data']['meta']['editable']) {
            // Inject TinyMCE
            $html .=
                '<!--{{postleaf_foot}}-->' .
                '<script src="' . htmlspecialchars(
                    \Postleaf\Postleaf::url(
                        'source/vendor/tinymce/tinymce/tinymce.min.js?v=' .
                        $options['data']['postleaf']['version']
                    )
                ) . '"></script>';
        }

        // Inject foot code
        $html .= "\n" . \Postleaf\Setting::get('foot_code');

        // Inject admin toolbar if the user is logged in and the post isn't editable or a preview
        if(
            \Postleaf\Session::isAuthenticated() &&
            !$options['data']['meta']['editable'] &&
            !$options['data']['meta']['preview']
        ) {
            // Render it
            $html .= \Postleaf\Renderer::render([
                'template' => \Postleaf\Postleaf::path(
                    'source/templates/partials/admin-toolbar.hbs'
                ),
                'data' => [
                    'items' => \Postleaf\Admin::getAdminToolbarItems(
                        $options['data']['template'],
                        $options['_this']
                    )
                ],
                'helpers' => ['url', 'utility', 'theme']
            ]);
        }

        // Return raw HTML
        return new LS($html);
    },
            'postleaf_head' => function($options) {
        $html = '';

        // If we're editing a post, add required code
        if($options['data']['meta']['editable']) {
            // Inject Postleaf data and editor stylesheet
            $html .=
                '<!--{{postleaf_head}}-->' .
                '<script>window.postleaf = true;</script>' .
                '<link rel="stylesheet" href="' . htmlspecialchars(
                    \Postleaf\Postleaf::url(
                        'source/assets/css/editor.css?v=' .
                        $options['data']['postleaf']['version']
                    )
                ) . '">';
        }

        // Inject admin toolbar styles if the user is logged in and it's not a preview/editable post
        if(
            \Postleaf\Session::isAuthenticated() &&
            !$options['data']['meta']['editable'] &&
            !$options['data']['meta']['preview']
        ) {
            $html .=
                '<link rel="stylesheet" href="' .
                htmlspecialchars(
                    \Postleaf\Postleaf::url(
                        'source/assets/css/admin-toolbar.css?v=' .
                        $options['data']['postleaf']['version']
                    )
                ) .
                '">';
        }

        // Inject head code
        $html .= "\n" . \Postleaf\Setting::get('head_code');

        // Inject JSON linked data (schema.org)
        if(isset($options['data']['meta']['ld_json'])) {
            $html .=
                "\n<script type=\"application/ld+json\">" .
                json_encode($options['data']['meta']['ld_json'], JSON_PRETTY_PRINT) .
                "</script>";
        }

        // Inject Open Graph data
        if(is_array($options['data']['meta']['open_graph'])) {
            foreach($options['data']['meta']['open_graph'] as $key => $value) {
                if($value === null) continue;
                $html .= "\n<meta property=\"" . htmlspecialchars($key) . "\" content=\"" . htmlspecialchars($value) . "\">";
            }
        }

        // Inject Twitter Card data
        if(is_array($options['data']['meta']['twitter_card'])) {
            foreach($options['data']['meta']['twitter_card'] as $key => $value) {
                if($value === null) continue;
                $html .= "\n<meta name=\"" . htmlspecialchars($key) . "\" content=\"" . htmlspecialchars($value) . "\">";
            }
        }

        // Return raw HTML
        return new LS($html);
    },
            'title' => function($options) {
        $title = $options['_this']['title'];
        $editable = mb_strtolower($options['hash']['editable']) === 'true';

        // Is the post being rendered in the editor?
        if($editable && $options['data']['meta']['editable']) {
            // If so, wrap in editable tags and output raw
            //
            // Note that content is also being inserted into the data-postleaf-html attribute inside
            // the div. We do this so we can grab the original markup once it's loaded into the
            // editor, as the code may have been altered by scripts.
            return new LS(
                '<div data-postleaf-id="post:title" data-postleaf-type="post-title" ' .
                'data-postleaf-html="' . htmlspecialchars($title) . '">' .
                htmlspecialchars($title) .
                '</div>'
            );
        } else {
            // Otherwise, just return the title as-is
            return $title;
        }
    },
);
    $partials = array();
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
    
    ob_start();echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>',lcr584880f0af486encq($cx, lcr584880f0af486v($cx, $in, isset($in) ? $in : null, array('meta_title'))),'</title>
        <meta name="description" content="',lcr584880f0af486encq($cx, lcr584880f0af486v($cx, $in, isset($in) ? $in : null, array('meta_description'))),'">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700%7CLato:700">
        <style>
            html {
                padding: 0;
                margin: 0;
            }
            body {
                font-size: 20px;
                font-family: Lora, serif;
                font-weight: 300;
                color: #333;
                padding: 2rem;
                line-height: 1.6;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
            }
            .title {
                font-family: Lato, sans-serif;
                font-size: 3rem;
                font-weight: 700;
                line-height: 1.25;
                margin: 0 0 2rem 0;
            }
            @media screen and (max-width: 600px) {
                body {
                    font-size: 18px;
                    padding: .5rem;
                }
                .title {
                    font-size: 2rem;
                }
            }
            a {
                color: #09d;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
            blockquote {
                border-left: solid 5px #eee;
                padding-left: 2rem;
                margin: 0 0 1.5rem 0;
            }
            code {
                font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
                background-color: #eee;
                border-radius: 4px;
                padding: 2px 4px;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: Lato, sans-serif;
                font-weight: 700;
                margin: 0 0 .5em 0;
            }
            hr {
                border: none;
                border-top: solid 1px #ddd;
                margin-bottom: 2rem;
            }
            img {
                max-width: 100%;
            }
            ol, ul {
                line-height: 1.8;
                list-style-position: inside;
                padding: 0;
                margin: 0 0 1.5rem 1.5rem;
            }
            p {
                margin: 0 0 1.5rem 0;
            }

            /* Mandatory editor alignment classes */
            .align-left {
                text-align: left;
            }
            .align-center {
                text-align: center;
            }
            .align-right {
                text-align: right;
            }
            .align-justify {
                text-align: justify;
            }
            img.align-left,
            [data-embed].align-left {
                float: left;
                margin-right: 1em;
            }
            img.align-center,
            [data-embed].align-center {
                display: block;
                margin-left: auto;
                margin-right: auto;
            }
            img.align-right,
            [data-embed].align-right {
                float: right;
                margin-left: 1em;
            }

            /* Hide outlines */
            body [data-postleaf-type="post-content"],
            body [data-postleaf-type="post-title"] {
                outline: none;
            }
        </style>
        ',lcr584880f0af486encq($cx, lcr584880f0af486hbch($cx, 'postleaf_head', array(array(),array()), 'encq', $in)),'
    </head>
    <body>
',lcr584880f0af486hbch($cx, 'post', array(array(),array()), $in, false, function($cx, $in) {echo '            <div class="container">
                <h1 class="title">',lcr584880f0af486encq($cx, lcr584880f0af486hbch($cx, 'title', array(array(),array('editable'=>'true')), 'encq', $in)),'</h1>
                <div class="content">
                    ',lcr584880f0af486encq($cx, lcr584880f0af486hbch($cx, 'content', array(array(),array('editable'=>'true')), 'encq', $in)),'
                </div>
            </div>
';}),'        ',lcr584880f0af486encq($cx, lcr584880f0af486hbch($cx, 'postleaf_foot', array(array(),array()), 'encq', $in)),'
    </body>
</html>';return ob_get_clean();
};