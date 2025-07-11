<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksAutomaticLinksModel extends SuperLinksCoreModel {

    protected $pages;

    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_automaticLinks']
        );
    }

    public function getModelName(){
        return 'SuperLinksAutomaticLinksModel';
    }

    public function rules()
    {
        return [
            [
                'title, url', 'required'
            ],
        ];
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Página de carregamento'),
            'page_id' => TranslateHelper::getTranslate('Página do site/blog'),
            'title' => TranslateHelper::getTranslate('Nome desta configuração de links'),
            'keywords' => TranslateHelper::getTranslate('Palavras-chave'),
            'url' => TranslateHelper::getTranslate('Url de redirecionamento'),
            'num' => TranslateHelper::getTranslate('Número de links'),
            'target' => TranslateHelper::getTranslate('Link target'),
            'nofollow' => TranslateHelper::getTranslate('Permitir os motores de busca seguirem este link'),
            'active' => TranslateHelper::getTranslate('Link ativo?'),
            'partly_match' => TranslateHelper::getTranslate('Permitir substituição parcial das palavras-chave?'),
            'titleattr' => TranslateHelper::getTranslate('Título do link'),
        );
    }

    public function setPages()
    {
        $search = '';

        if(isset($_POST['search'])){
            $search = $_POST['search'];
        }

        $posts = SuperLinksPosts::query()
            ->select('ID as id, post_title as title, post_type as type')
            ->where('post_type', 'in', [array_values(get_post_types(['public' => true]))])
            ->where('post_title', 'like', '%' . SuperLinksPosts::wpdb()->esc_like($search) . '%')
            ->where('post_status', 'publish')
            ->order_by('post_modified', 'desc')
            ->limit(25)
            ->get_results();

       $this->pages = $posts;
    }

    public function getPages(){
        return $this->pages;
    }

    public function getPagesValues(){
        $valuesPages[] = ['selected' => true, 'text' => 'Todas as páginas', 'val' => ''];

        foreach($this->getPages() as $page){
            $valuesPages[] = ['selected' => false, 'text' => $page['title'] . ' (' . $page['type'] . ')', 'val' => $page['id']];
        }

        return $valuesPages;
    }

    public function getKeywords()
    {
        $keywords = $this->getAttribute('keywords');

        if(!is_array($keywords)) {
            if(is_null($keywords)){
                $keywords = '';
            }
            $keywords = explode(",", $keywords);
            $keywords = array_filter($keywords);
            $keywords = array_values($keywords);
        }

        return $keywords;
    }

    public function getReplacePattern()
    {
        $keywordsInDatabase = $this->getKeywords();

        $keywords = [];

        // add html entities versions
        foreach ($keywordsInDatabase as $keyword) {
            $trimmedKeyword = $keyword;
            if(empty($trimmedKeyword)) {
                // do not add empty keywords
                continue;
            }
            // first add original keyword
            $keywords[] = $trimmedKeyword;

            // escape keyword and compare with original
            $escapedKeyword = esc_html($trimmedKeyword);
            if($escapedKeyword !== $trimmedKeyword) {
                // add escaped keyword to list
                $keywords[] = $escapedKeyword;
            }

            // texturize keyword with wordpress function and compare with original
            $escapedKeyword2 = wptexturize($trimmedKeyword);
            if($escapedKeyword2 !== $trimmedKeyword && $escapedKeyword2 !== $escapedKeyword) {
                // add escaped keyword to list
                $keywords[] = $escapedKeyword2;
            }
        }

        // create regex strings out of keywords
        $self = $this;
        $keywords = array_map(function($str) use($self) {
            $quoted = preg_quote($str, '/');
            if($self->getAttribute('partly_match')) {
                return $quoted;
            }
            return '(?:(?<!\w))' . $quoted . '(?:(?!\w))';
        }, $keywords);

        return '/' . join('|', $keywords) . '/ui';
    }

    public function getReplaceString($match)
    {
        $url = $this->getAttribute('url');
        $title = $this->getAttribute('titleattr');

        if($this->getAttribute('page_id')) {
            $post = get_post($this->getAttribute('page_id'));
            if($post) {
                $url = get_permalink($post);
                if(empty($title)) {
                    $title = $post->post_title;
                }
            }
        }

        $dataHash = "superlinks140684vt908f2723b";
        $attrs = [
            'href' => $url,
            "data-$dataHash" => $this->getAttribute('id'),
        ];

        if(!$this->getAttribute('title')) {
            $attrs['title'] = $title;
            if(empty($attrs['title'])) {
                $attrs['title'] = $this->getAttribute('title');
            }
        }

        if($this->getAttribute('nofollow')) {
            $attrs['rel'] = 'nofollow';
        }

        if($this->getAttribute('target') === '_blank') {
            $attrs['target'] = '_blank';
            if(array_key_exists('rel', $attrs)) {
                $attrs['rel'] .= ' noopener';
            } else {
                $attrs['rel'] = 'noopener';
            }
        }

        $attrs['class'] = 'spl-automatic-Link';
        $attrs['data-spl'] = $this->getAttribute('id');

        // generate html string out of attributes
        $attrsFlat = [];
        foreach ($attrs as $name => $value) {
            $encodedValue = htmlspecialchars($value);
            $attrsFlat[] = "$name=\"$encodedValue\"";
        }
        $attrsStr = join(' ', $attrsFlat);

        return "<a $attrsStr>$match</a>";
    }

    public function getLinksByIDGroup($idGroup = null){
        global $wpdb;
        $tableName = $this->getTableName();
        if(is_null($tableName)){
            return [];
        }

        if(is_null($idGroup)){
            return $wpdb->get_results("SELECT * FROM $tableName where idGroup IS NULL");
        }

        return $wpdb->get_results("SELECT * FROM $tableName where idGroup = '".$idGroup."'");
    }
}