<?php  function lcr57f1ac1139d44hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr57f1ac1139d44m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57f1ac1139d44m($cx, $context, $ex) : $context);
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
   lcr57f1ac1139d44err($cx, $e);
  }

  return $r;
 }
 function lcr57f1ac1139d44encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57f1ac1139d44raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57f1ac1139d44v($cx, $in, $base, $path, $args = null) {
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
   lcr57f1ac1139d44err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57f1ac1139d44sec($cx, $v, $bp, $in, $each, $cb, $else = null) {
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
     $raw = lcr57f1ac1139d44m($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr57f1ac1139d44m($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
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
 function lcr57f1ac1139d44ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57f1ac1139d44p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57f1ac1139d44err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57f1ac1139d44m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57f1ac1139d44m($cx, $a, $b) {
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
 function lcr57f1ac1139d44err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57f1ac1139d44raw($cx, $v) {
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
      $ret[] = lcr57f1ac1139d44raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
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
    $helpers = array(            'author' => function($slug, $options = null) {
        if(!$options) {
            $options = $slug;

            if(is_array($options['_this']['author'])) {
                // Try this.author (array)
                $author = $options['_this']['author'];
            } elseif(is_string($options['_this']['author'])) {
                // Try this.author
                $author = \Postleaf\User::get($options['_this']['author']);
            }
        } else {
            // Get the author by slug
            $author = \Postleaf\User::get($slug);
        }

        // Do {{else}} if no author is found
        if(!$author) {
            return $options['inverse'] ? $options['inverse']() : '';
        }

        // Remove sensitive data
        unset($author['password'], $author['reset_token']);

        return $options['fn']($author);
    },
            'bio' => function($options) {
        return new LS(
            \Postleaf\Postleaf::markdownToHtml($options['_this']['bio'])
        );
    },
            'body_class' => function($options) {
        // Template class
        $class = $options['data']['template'] . '-template';

        // Homepage class
        if(\Postleaf\Postleaf::isHomepage()) {
            $class .= ' homepage';
        }

        // Pagination class
        if(isset($options['_this']['pagination'])) {
            $class .= ' page-' . $options['_this']['pagination']['current_page'];
        }

        return $class;
    },
            'content' => function($options) {
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
            'navigation' => function($options) {
        // Decode nav from settings
        $items = (array) json_decode(\Postleaf\Setting::get('navigation'), true);

        // Generate `slug` and `current` values for each nav item
        foreach($items as $key => $value) {
            $items[$key]['slug'] = \Postleaf\Postleaf::slug($value['label']);
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
            'next_post' => function($slug, $options = null) {
        if(!$options) {
            $options = $slug;

            if(is_array($options['_this']['post'])) {
                // Try this.post.slug
                $slug = $options['_this']['post']['slug'];
            } elseif(isset($options['_this']['slug'])) {
                // Try this.slug
                $slug = $options['_this']['slug'];
            } else {
                // Nothing to fallback to
                $slug = null;
            }
        }

        // Get the previous post
        $post = \Postleaf\Post::getAdjacent($slug, [
            'direction' => 'next',
            'author' => $options['hash']['author'],
            'tag' => $options['hash']['tag']
        ]);

        // Was a post found?
        if(is_array($post)) {
            // Yep, change context
            return $options['fn']((array) $post);
        } else {
            // No post, do {{else}}
            return $options['inverse'] ? $options['inverse']() : '';
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
            'post_class' => function($options) {
        $post = $options['_this'];

        // Build class
        $class = 'post';
        if($post['type'] === 'page') $class .= ' page';
        if($post['featured']) $class .= ' post-featured';
        if($post['sticky']) $class .= ' post-sticky';
        if($post['image']) $class .= ' post-image';
        foreach((array) $post['tags'] as $tag) {
            $class .= ' tag-' . $tag;
        }

        return $class;
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
            'previous_post' => function($slug, $options = null) {
        if(!$options) {
            $options = $slug;

            if(is_array($options['_this']['post'])) {
                // Try this.post.slug
                $slug = $options['_this']['post']['slug'];
            } elseif(isset($options['_this']['slug'])) {
                // Try this.slug
                $slug = $options['_this']['slug'];
            } else {
                // Nothing to fallback to
                $slug = null;
            }
        }

        // Get the previous post
        $post = \Postleaf\Post::getAdjacent($slug, [
            'direction' => 'previous',
            'author' => $options['hash']['author'],
            'tag' => $options['hash']['tag']
        ]);

        // Was a post found?
        if(is_array($post)) {
            // Yep, change context
            return $options['fn']((array) $post);
        } else {
            // No post, do {{else}}
            return $options['inverse'] ? $options['inverse']() : '';
        }
    },
            'reading_time' => function($string, $options = null) {
        if(!$options) {
            $options = $string;

            if(isset($options['_this']['content'])) {
                // Try this.content
                $string = strip_tags($options['_this']['content']);
            } else {
                $string = '';
            }
        }

        // Get words per minute
        $words_per_minute = (int) $options['hash']['words_per_minute'];
        if($words_per_minute < 1) $words_per_minute = 225;

        // Get number of words
        $num_words = str_word_count($string);

        // Calculate average reading time in minutes (minimum 1 min)
        return max(1, ceil($num_words / $words_per_minute));
    },
            'tags' => function($slugs, $options = null) {
        if(!$options) {
            $options = $slugs;

            if(isset($options['_this']['tags'])) {
                // Try this.tags
                $slugs = $options['_this']['tags'];
            } else {
                $slugs = null;
            }
        }

        // Convert CSV slugs to array
        if(is_string($slugs)) {
            $slugs = array_map('trim', explode(',', $slugs));
        }

        // Get data for each tag
        $tags = [];
        foreach((array) $slugs as $slug) {
            $tag = \Postleaf\Tag::get($slug);
            if($tag) $tags[] = $tag;
        }

        // Get attributes
        $before = (string) $options['hash']['before'];
        $after = (string) $options['hash']['after'];
        $and = (string) $options['hash']['and'];
        $autolink = mb_strtolower($options['hash']['autolink']) !== 'false';
        $separator = isset($options['hash']['separator']) ? $options['hash']['separator'] : ', ';

        // Sort tags by name
        if(is_array($tags)) {
            usort($tags, function($a, $b) {
                return mb_strtolower($a['name']) > mb_strtolower($b['name']);
            });
        }

        // Append each tag
        $content = [];
        foreach((array) $tags as $tag) {
            $c = '';
            if($autolink) {
                $c .=
                    '<a href="' . htmlspecialchars( \Postleaf\Tag::url($tag['slug']) ) . '">' .
                    htmlspecialchars($tag['name']) .
                    '</a>';
            } else {
                $c .= htmlspecialchars($tag['name']);
            }

            $content[] = $c;
        }

        // Add separators
        if(count($tags) > 1 && !empty($and)) {
            // If $and is set: tag1, tag2 and tag4
            $left = array_slice($content, 0, count($content) - 1);
            $right = $content[count($content) - 1];
            $content = implode($separator, $left) . $and . $right;
        } else {
            // If $and isn't set: tag1, tag2, tag3
            $content = implode($separator, $content);
        }

        // Add before/after if at least one tag exists
        if(count($tags)) $content = $before . $content . $after;

        return $autolink ? new LS($content) : $content;
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
            'author_url' => function($author, $options = null) {
        if(!$options) {
            $options = $author;

            if(isset($options['_this']['author'])) {
                // Try this.author
                $author = $options['_this']['author'];
            } elseif(isset($options['_this']['slug'])) {
                // Try this.slug
                $author = $options['_this']['slug'];
            } else {
                // Nothing to fall back to
                return '';
            }
        }

        return \Postleaf\User::url($author, (int) $options['hash']['page']);
    },
            'feed_url' => function($options) {
        // Get hash arguments
        $author = $options['hash']['author'];
        $tag = $options['hash']['tag'];

        // Set feed options
        $feed_options = [];
        if($author) $feed_options['author'] = $author;
        if($tag) $feed_options['tag'] = $tag;

        return \Postleaf\Feed::url($feed_options);
    },
            'post_url' => function($slug, $options = null) {
        if(!$options) {
            $options = $slug;

            if(isset($options['_this']['slug'])) {
                // Try this.slug
                $slug = $options['_this']['slug'];
            } else {
                return '';
            }
        }

        return \Postleaf\Post::url($slug);
    },
            'search_url' => function($options) {
        $query = (string) $options['hash']['query'];
        $page = (int) $options['hash']['page'];

        // Empty queries get pushed to page 1
        if(!$query) $page = 1;

        return \Postleaf\Search::url($query, $page);
    },
            'theme_url' => function($path, $options = null) {
        return $options ?
            \Postleaf\Postleaf::url('content/themes', \Postleaf\Setting::get('theme'), $path) :
            \Postleaf\Postleaf::url('content/themes', \Postleaf\Setting::get('theme'));
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
            'encode' => function($string) {
        return rawurlencode($string);
    },
);
    $partials = array('navigation' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<div class="nav">
',$sp,'
',$sp,'    <form class="search" action="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'search_url', array(array(),array()), 'encq', $in)),'" autocomplete="off">
',$sp,'        <input type="search" name="s" placeholder="Search" class="form-control">
',$sp,'    </form>
',$sp,'
',$sp,'    <nav>
',$sp,'',lcr57f1ac1139d44hbch($cx, 'navigation', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57f1ac1139d44sec($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a class="nav-',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('slug'))),' ';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current';}else{echo '';}echo '" href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('link'))),array()), 'encq', $in)),'">
',$sp,'                    ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('label'))),'
',$sp,'                </a>
',$sp,'';}),'';}),'    </nav>
',$sp,'
',$sp,'    <a class="feed" href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'feed_url', array(array(),array()), 'encq', $in)),'">
',$sp,'        RSS Feed
',$sp,'    </a>
',$sp,'
',$sp,'</div>';return ob_get_clean();},
'header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('meta','title'))),'</title>
',$sp,'    <meta name="description" content="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('meta','description'))),'">
',$sp,'
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta name="viewport" content="width=device-width,initial-scale=1.0">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'
',$sp,'    ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'postleaf_head', array(array(),array()), 'encq', $in)),'
',$sp,'
',$sp,'    <link rel="shortcut icon" href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon'))),array()), 'encq', $in)),'">
',$sp,'
',$sp,'    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" integrity="sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY" crossorigin="anonymous">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather:300,700%7CLato:300,700">
',$sp,'
',$sp,'    <link rel="stylesheet" href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'theme_url', array(array('css/theme.css'),array()), 'encq', $in)),'">
',$sp,'</head>
',$sp,'
',$sp,'<body class="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'body_class', array(array(),array()), 'encq', $in)),'">
',$sp,'
',$sp,'',lcr57f1ac1139d44p($cx, 'navigation', array(array($in),array()),0, '    '),'
',$sp,'    <div class="body-wrap">
',$sp,'
',$sp,'';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo')), false)){echo '            <a href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" class="logo">
',$sp,'                <img src="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo'))),array()), 'encq', $in)),'" alt="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'">
',$sp,'            </a>
',$sp,'';}else{echo '';}echo '
',$sp,'        <a href="#" class="nav-toggle">Menu</a>';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'        <footer>
',$sp,'            <div class="container">
',$sp,'                <div class="row">
',$sp,'                    <div class="col-xs-6">
',$sp,'                        <a href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(),array()), 'encq', $in)),'">',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</a> &copy;',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'date', array(array(),array('format'=>'%Y')), 'encq', $in)),'
',$sp,'                    </div>
',$sp,'                    <div class="col-xs-6 text-xs-right">
',$sp,'                        <a href="http://colegiulunirea.ro/">Colegiul Național „Unirea”</a>
',$sp,'                    </div>
',$sp,'                </div>
',$sp,'            </div>
',$sp,'        </footer>
',$sp,'
',$sp,'    </div> 
',$sp,'
',$sp,'    ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'postleaf_foot', array(array(),array()), 'encq', $in)),'
',$sp,'
',$sp,'    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
',$sp,'    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
',$sp,'    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js" integrity="sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD" crossorigin="anonymous"></script>
',$sp,'
',$sp,'    <script src="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'theme_url', array(array('js/theme.js'),array()), 'encq', $in)),'"></script>
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
    
    ob_start();echo '',lcr57f1ac1139d44p($cx, 'header', array(array($in),array()),0),'
',lcr57f1ac1139d44hbch($cx, 'post', array(array(),array()), $in, false, function($cx, $in) {echo '    <header class="cover" ';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('image')), false)){echo 'style="background-image: url(\'',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('image'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '>
        <div class="content">
            <h1 class="title">',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'title', array(array(),array('editable'=>'true')), 'encq', $in)),'</h1>
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-md-8 push-md-2 col-sm-10 push-sm-1">
                <article class="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'post_class', array(array(),array()), 'encq', $in)),'">
                    <p class="description">
                        <span class="reading-time">
                            ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'reading_time', array(array(),array()), 'encq', $in)),' min read
                        </span>

';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('featured')), false)){echo '                            <span class="featured">
                                Featured
                            </span>
';}else{echo '';}echo '
';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('sticky')), false)){echo '                            <span class="sticky">
                                Sticky
                            </span>
';}else{echo '';}echo '
                        <span class="date">
                            ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'date', array(array(),array('format'=>'%B %e, %Y at %l:%M%P')), 'encq', $in)),'
                        </span>

                        <span class="tags">
                            ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'tags', array(array(),array('before'=>'on ','and'=>' and ')), 'encq', $in)),'
                        </span>
                    </p>

                    <div class="content">
                        ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'content', array(array(),array('editable'=>'true')), 'encq', $in)),'
                    </div>

                    <div class="post-pagination">
',lcr57f1ac1139d44hbch($cx, 'previous_post', array(array(),array()), $in, false, function($cx, $in) {echo '                            <div class="previous">
                                <a href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'post_url', array(array(),array()), 'encq', $in)),'">Previous Post</a>
                            </div>
';}),'
',lcr57f1ac1139d44hbch($cx, 'next_post', array(array(),array()), $in, false, function($cx, $in) {echo '                            <div class="next">
                                <a class="btn btn-secondary" href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'post_url', array(array(),array()), 'encq', $in)),'">Next Post</a>
                            </div>
';}),'                    </div>
                </article>
            </div>
        </div>

        <div class="col-md-8 push-md-2 col-sm-10 push-sm-1">
            <div class="row">
                <div class="footer">
                    <div class="author col-md-8">
',lcr57f1ac1139d44hbch($cx, 'author', array(array(),array()), $in, false, function($cx, $in) {echo '';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                                <a href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'author_url', array(array(),array()), 'encq', $in)),'">
                                   <img class="author-avatar" src="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'url', array(array(lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('name'))),'">
                                </a>
';}else{echo '';}echo '
                            <h4 class="author-name">
                                <a href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'author_url', array(array(),array()), 'encq', $in)),'">',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('name'))),'</a>
                            </h4>

                            <div class="author-bio">
                                ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'bio', array(array(),array()), 'encq', $in)),'
                            </div>

                            <div class="author-details">
';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('location')), false)){echo '                                    <span class="author-location">
                                        <span class="icon icon-location"></span>
                                        ',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('location'))),'
                                    </span>
';}else{echo '';}echo '
';if (lcr57f1ac1139d44ifvar($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('website')), false)){echo '                                    <span class="author-website">
                                        <span class="icon icon-link"></span>
                                        <a href="',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('website'))),'">',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('website'))),'</a>
                                    </span>
';}else{echo '';}echo '                            </div>
';}),'                    </div>

                    <div class="share col-md-4">
                        Share This Post<br>
                        <a class="share-icon icon icon-twitter" href="https://twitter.com/intent/tweet?text=',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'encode', array(array(lcr57f1ac1139d44v($cx, $in, isset($in) ? $in : null, array('title'))),array()), 'encq', $in)),'&amp;url=',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'encode', array(array(lcr57f1ac1139d44hbch($cx, 'post_url', array(array(),array()), 'raw', $in)),array()), 'encq', $in)),'">
                            <span class="hidden">Share on Twitter</span>
                        </a>
                        <a class="share-icon icon icon-facebook" href="https://www.facebook.com/sharer/sharer.php?u=',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'encode', array(array(lcr57f1ac1139d44hbch($cx, 'post_url', array(array(),array()), 'raw', $in)),array()), 'encq', $in)),'">
                            <span class="hidden">Share on Facebook</span>
                        </a>
                        <a class="share-icon icon icon-googleplus" href="https://plus.google.com/share?url=',lcr57f1ac1139d44encq($cx, lcr57f1ac1139d44hbch($cx, 'encode', array(array(lcr57f1ac1139d44hbch($cx, 'post_url', array(array(),array()), 'raw', $in)),array()), 'encq', $in)),'">
                            <span class="hidden">Share on Google+</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
';}),'
',lcr57f1ac1139d44p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};