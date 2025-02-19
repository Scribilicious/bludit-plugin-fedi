<?php

class PluginFedi extends Plugin
{
    /**
     * Initialize
     */
    public function init()
    {
        $this->dbFields = [
            'url' => '',
            'key' => '',
            'shortcut' => 0,
            'reply_help_direct' => 'Comments are powered by Mastodon. With an account on Mastodon (or elsewhere on the Fediverse), you can respond to this post. Simply enter your Mastodon instance below, and add a reply:',
            'reply_help_copy' => 'Alternatively, copy this URL and paste it into the search bar of your Mastodon app:',
            'styles' => '
:root {
  --comments-foreground: #1e2030;
  --comments-background: #f8f9fa;
  --comments-accent: #868e96;
}
#comments dialog {
  width: 30em;
  padding: 1em;
  color: var(--comments-foreground);
  background: var(--comments-background);
  box-sizing: border-box;
}
#comments dialog::backdrop {
  background-color: rgba(0, 0, 0, 0.5);
}
#comments dialog h3 {
  margin-top: 0.4rem;
}
#comments #close {
  position: absolute;
  top: 0;
  right: 0;
  background: none;
  color: inherit;
  border: none;
  font: inherit;
  outline: inherit;
  padding: 0 0.2em;
  font-size: 3em;
}
#comments .input-row {
  display: -webkit-box; /* Add -webkit-box for older Safari */
  display: -moz-box; /* Add -moz-box for older Firefox */
  display: -ms-flexbox; /* Add -ms-flexbox for older IE */
  display: -webkit-flex; /* Add -webkit-flex for older Safari */
  display: flex;
}
#comments .input-row > * {
  display: block;
}
#comments button {
  background: none;
  color: inherit;
  border: none;
  cursor: pointer;
  outline: inherit;
  background-color: var(--comments-accent);
  padding: 0.25em 1em;
  border-radius: 0.5em;
  color: var(--comments-background);
  font-size: 16px;
}
#comments input {
  padding: 0.25em 1em;
  border-radius: 0.5em;
  border-width: 1px;
  border-color: var(--comments-accent);
  font-size: 16px;
}
#comments .addComment {
  display: block;
  width: 100%;
  font-size: 100%;
  text-align: center;
}
#comments .comment-list .comment {
  display: -webkit-box; /* Add -webkit-box for older Safari */
  display: -moz-box; /* Add -moz-box for older Firefox */
  display: -ms-flexbox; /* Add -ms-flexbox for older IE */
  display: -webkit-flex; /* Add -webkit-flex for older Safari */
  display: flex;
  padding: 0.5em;
  border-width: 1px;
  border-style: solid;
  border-color: var(--comments-accent);
  border-radius: 0.5em;
  margin-bottom: 1em;
}
#comments .comment-list .comment .avatar {
  -webkit-box-flex: 0; /* Add -webkit-box-flex for older Safari */
  -moz-box-flex: 0; /* Add -moz-box-flex for older Firefox */
  -ms-flex: 0; /* Add -ms-flex for older IE */
  flex-grow: 0;
  flex-shrink: 0;
  width: 70px;
}
#comments .comment-list .comment .content {
  -webkit-box-flex: 1; /* Add -webkit-box-flex for older Safari */
  -moz-box-flex: 1; /* Add -moz-box-flex for older Firefox */
  -ms-flex: 1; /* Add -ms-flex for older IE */
  flex-grow: 1;
}
#comments .comment-list .comment .author {
  width: 100%;
  display: -webkit-box; /* Add -webkit-box for older Safari */
  display: -moz-box; /* Add -moz-box for older Firefox */
  display: -ms-flexbox; /* Add -ms-flexbox for older IE */
  display: -webkit-flex; /* Add -webkit-flex for older Safari */
  display: flex;
}
#comments .comment-list .comment .author > * {
  -webkit-box-flex: 1; /* Add -webkit-box-flex for older Safari */
  -moz-box-flex: 1; /* Add -moz-box-flex for older Firefox */
  -ms-flex: 1; /* Add -ms-flex for older IE */
  flex-grow: 1;
}
#comments .comment-list .comment .author .date {
  margin-left: auto;
  text-align: right;
}

@media screen and (max-width: 850px) {
  #comments .comment-list .comment .author {
    display: block;
  }
  #comments .comment-list .comment .author > * {
    display: block;
  }
  #comments .comment-list .comment .author .date {
    font-size: 0.8em;
    text-align: left;
    margin-left: 0;
  }
}
            '
        ];
    }

    /**
     * Ads a link in the sidebar
     */
    // phpcs:ignore error
    public function adminSidebar()
    {
        if ($this->getValue('shortcut')) {
            $url = HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . __CLASS__;
            $html = '<a id="current-version" class="nav-link" href="' . $url . '"><span class="fa fa-gear"></span>' . str_replace('Plugin', '', __CLASS__) . ' Settings</a>';
            return $html;
        }
    }

    /**
     * Creates the config form
     */
    public function form()
    {
        global $L;

        $html = $this->payMe();

        $html .= '<div class="alert alert-primary" role="alert">';
        $html .= $L->get('Fedi Help');
        $html .= '</div><hr>';

        $html .= '<div>';
        $html .= '<label>' . $L->get('Fedi Host') . '</label>';
        $html .= '<input name="url" type="text" value="' . $this->getValue('url') . '">';
        $html .= '<span class="tip">' . $L->get('Fedi Host Tip') . '</span>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>' . $L->get('Fedi User') . '</label>';
        $html .= '<input name="user" type="text" value="' . $this->getValue('user') . '">';
        $html .= '<span class="tip">' . $L->get('Fedi User Tip') . '</span>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>' . $L->get('Fedi Key') . '</label>';
        $html .= '<input name="key" type="text" value="' . $this->getValue('key') . '">';
        $html .= '<span class="tip">' . $L->get('Fedi Key Tip') . '</span>';
        $html .= '</div><hr>';

        $html .= '<div>';
        $html .= '<label>' . $L->get('Fedi Dialog Reply') . '</label>';
        $html .= '<input name="reply_help_direct" type="text" value="' . $this->getValue('reply_help_direct') . '">';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>' . $L->get('Fedi Dialog Copy') . '</label>';
        $html .= '<input name="reply_help_copy" type="text" value="' . $this->getValue('reply_help_copy') . '">';
        $html .= '</div><hr>';

        $html .= '<div>';
        $html .= '<label>' . $L->get('Fedi Styles') . '</label>';
        $html .= '<textarea name="styles" id="jstext" rows="20">' . htmlspecialchars($this->getValue('styles')) . '</textarea>';
        $html .= '</div><hr>';

        $html .= '<div>';
        $html .= '<label>'.$L->get('Shortcut').'</label>';
        $html .= '<select name="shortcut">';
        $html .= '<option value="0" ' . ($this->getValue('shortcut') === 0 ? 'selected' : '') . '>-</option>';
        $html .= '<option value="1" ' . ($this->getValue('shortcut') === 1 ? 'selected' : '') . '>' . $L->get('Show shortcut') . '</option>';
        $html .= '</select>';
        $html .= '</div>';

        $html .= $this->footer();

        return $html;
    }

    /**
     * Check before saving
     */
    public function post()
    {
        $this->db['url'] = trim(trim(filter_var($_POST['url'], FILTER_SANITIZE_URL)), '/');
        $this->db['user'] = trim(trim(filter_var($_POST['user'], FILTER_SANITIZE_STRING)), '@');
        $this->db['reply_help_direct'] = trim(trim(filter_var($_POST['reply_help_direct'], FILTER_SANITIZE_STRING)), '@');
        $this->db['reply_help_copy'] = trim(trim(filter_var($_POST['reply_help_copy'], FILTER_SANITIZE_STRING)), '@');
        $this->db['key'] = trim(filter_var($_POST['key'], FILTER_SANITIZE_URL));
        $this->db['styles'] = trim(htmlspecialchars_decode($_POST['styles']));
        $this->db['shortcut'] = intval($_POST['shortcut']);

        // Save the database
        return $this->save();
    }

    public function siteHead()
    {
        echo '<style>' . $this->getValue('styles') . '</style>';
    }

    /**
     * Find the macro and replace it
     */
    public function pageBegin()
    {
        $page = $GLOBALS['page']->content();
        $pattern = '/\[\[FEDI::(.*?)\]\]/s';
        preg_match_all($pattern, $page, $matches, PREG_SET_ORDER);

        if (empty($matches[0][1])) {
            return;
        }

        $id = trim($matches[0][1]);
        $url_api = Theme::siteUrl() . 'fedi/' . $id;
        $url_fedi = $this->getValue('url') . '/@' . $this->getValue('user') . '/statuses/' . $id;

        ob_start();
        include 'comments.tpl.php';
        $output = ob_get_clean();

        $page = str_replace($matches[0][0], $output, $page);

        $GLOBALS['page']->setField('content', $page);
    }

    /**
     * Creates the Support Me Button...
     */
    private function payMe()
    {
        global $L;

        $icons = ['💸', '🥹', '☕️', '🍻', '👾', '🍕'];
        shuffle($icons);
        $html = '<div class="bg-light text-center border mt-3 p-3">';
        $html .= '<p class="mb-2">' . $L->get('Please support Mr.Bot') . '</p>';
        $html .= '<a style="background: #ffd11b;box-shadow: 2px 2px 5px #ccc;padding: 0 10px;border-radius: 50%;width: 60px;display: block;text-align: center;margin: auto;height: 60px; font-size: 40px; line-height: 60px;" href="https://www.buymeacoffee.com/iambot" target="_blank" title="Buy me a coffee...">' . $icons[0] . '</a>';
        $html .= '</div><br>';

        return $html;
    }

    /**
     * Creates the Footer
     */
    private function footer()
    {
        $html = '<div class="text-center mt-3 p-3" style="opacity: 0.6;">';
        $html .= '<p class="mb-2">© ' . date('Y') . ' by <a href="https://github.com/Scribilicious" target="_blank" title="Visit GitHub page...">Mr.Bot</a>, Licensed under <a href="https://raw.githubusercontent.com/Scribilicious/MIT/main/LICENSE" target="_blank" title="view license...">MIT</a>.</p>';
        $html .= '</div><br>';

        return $html;
    }

    /**
     * Run before all
     */
    public function beforeAll()
    {
        global $url;
        global $pages;
        global $users;

        $data = null;

        $uri = $this->webhook('fedi', $returnsAfterURI = true, $fixed = false);
        if ($uri === false) {
            return false;
        }

        $params = explode('/', $uri);
        if (empty($params[1])) {
            $this->response(400, 'Bad Request', array('message' => 'Missing endpoint parameters.'));
        }

        $token = $this->getValue('key');
        if (!$token) {
            $this->response(400, 'Bad Request', array('message' => 'Needs a token.'));
        }

        $data = $this->call($token, $params[1]);
        if (!$data) {
            $this->response(400, 'Bad Request', array('message' => 'Error response from API.'));
        }

        $this->response(200, 'OK', $data);
    }

    private function call($token, $id)
    {
        $url = $this->getValue('url') . '/api/v1/statuses/' . $id . '/context';
        $bearerToken = $token;
        $userAgent = 'YourAppName/1.0';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $bearerToken,
            'User-Agent: ' . $userAgent,
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $data = null;
        } else {
            $data = json_decode($response, true);
        }

        curl_close($ch);

        return $data;
    }

    private function response($code = 200, $message = 'OK', $data = array())
    {
        header('HTTP/1.1 ' . $code . ' ' . $message);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $json = json_encode($data);
        die($json);
    }
}
