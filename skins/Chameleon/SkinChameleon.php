<?php
/**
 * OpenSUSE Chameleon Skin
 *
 * Follow openSUSE Branding Guidelines
 */

if (!defined( 'MEDIAWIKI' )) {
    die();
}

class SkinChameleon extends SkinTemplate
{
    public $skinname  = 'chameleon';
    public $stylename = 'chameleon';
    public $template  = 'ChameleonTemplate';
    public $useHeadElement = true;

    function initPage(OutputPage $out)
    {
        parent::initPage( $out );
        $out->addMeta( 'viewport', 'width=device-width, initial-scale=1' );
        $out->addModuleStyles( 'skins.chameleon' );
        $out->addModules( 'skins.chameleon.js' );
    }

    function setupSkinUserCss(OutputPage $out)
    {
        parent::setupSkinUserCss( $out );
    }
}


class ChameleonTemplate extends BaseTemplate
{
    var $skin;

    function xmlns()
    {
        foreach ($this->data['xhtmlnamespaces'] as $tag => $ns) {
            echo "xmlns:{$tag}=\"{$ns}\" ";
        }
    }

    function execute()
    {
        global $wgRequest, $wgStylePath;
        $this->skin = $skin = $this->data['skin'];
        $action = $wgRequest->getText( 'action' );

        // Build additional attributes for navigation urls
        $nav = $this->data['content_navigation'];

        $xmlID = '';
        foreach ($nav as $section => $links) {
            foreach ($links as $key => $link) {
                if ($section == 'views' && !( isset( $link['primary'] ) && $link['primary'] )) {
                    $link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
                }

                $xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
                $nav[$section][$key]['attributes'] =
                    ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
                if ($link['class']) {
                    $nav[$section][$key]['attributes'] .=
                        ' class="' . htmlspecialchars( $link['class'] ) . '"';
                    unset( $nav[$section][$key]['class'] );
                }
                if (isset( $link['tooltiponly'] ) && $link['tooltiponly']) {
                    $nav[$section][$key]['key'] =
                        Linker::tooltip( $xmlID );
                } else {
                    $nav[$section][$key]['key'] =
                        Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
                }
            }
        }
        $this->data['namespace_urls'] = $nav['namespaces'];
        $this->data['view_urls'] = $nav['views'];
        $this->data['action_urls'] = $nav['actions'];
        $this->data['variant_urls'] = $nav['variants'];

        // Reverse horizontally rendered navigation elements
        if ($this->data['rtl']) {
            $this->data['view_urls'] =
                array_reverse( $this->data['view_urls'] );
            $this->data['namespace_urls'] =
                array_reverse( $this->data['namespace_urls'] );
            $this->data['personal_urls'] =
                array_reverse( $this->data['personal_urls'] );
        }

        $this->data['login_url'] = '/ICSLogin/auth-up';
        $this->data['signup_url'] = "https://secure-www.novell.com/selfreg/jsp/createOpenSuseAccount.jsp?login=Sign+up";

        if ($this->data['username']) {
            $user = User::newFromName( $this->data['username'] );
            $this->data['gravatar'] = "https://www.gravatar.com/avatar/" . md5( $user->getEmail() );
        }

        $this->html( 'headelement' );
?>

<!-- Global Navbar -->
<?php include(__DIR__ . '/parts/global-navbar.php'); ?>

<!-- Main Wrap -->
<div id="main-wrap" class="container-fluid">
    <div class="row">

        <?php include(__DIR__ . '/parts/sidebar.php'); ?>
        
        <div class="col-md-8 col-lg-6 col-xl-8">
            <div class="container-fluid">
                
                <div id="mw-page-base" class="noprint"></div>
                <div id="mw-head-base" class="noprint"></div>

                <!-- Page Header -->
                <header id="mw-head" class="my-3">
                    
                    <div id="search-and-user" class="d-flex justify-content-between justify-content-md-end">
                        <!-- Search Form -->
                        <form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="form-inline">
                            <div class="input-group">
                                <?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'class' => 'form-control', 'type' => 'search' ) ); ?>
                            </div>
                        </form>

                        <!-- User Menu -->
                        <?php if ($this->data['username'] == null) : ?>

                            <!-- Login Menu -->
                            <div class="dropdown ml-2">
                                <button class="btn btn-primary" type="button" id="user-menu-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo $this->msg('login') ?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="<?php echo $this->data['signup_url'] ?>"><?php echo $this->msg('createaccount') ?></a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#login-modal"><?php echo $this->msg('login') ?></a>
                                </div>
                            </div><!-- /.dropdown -->

                            <!-- Login Modal -->
                            <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $this->data['login_url'] ?>" method="post" enctype="application/x-www-form-urlencoded" name="login_form">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel"><?php echo $this->msg('login') ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <input name="url" value="https://<?php echo $_SERVER['SERVER_NAME'] . htmlentities($_SERVER['REQUEST_URI']) ?>" type="hidden">
                                                <input name="return_to_path" value="<?php echo htmlentities($_SERVER['REQUEST_URI']) ?>" type="hidden">
                                                <input name="context" value="default" type="hidden"/>
                                                <input name="proxypath" value="reverse" type="hidden"/>
                                                <input name="message" value="Please log In" type="hidden"/>

                                                <div class="form-group">
                                                    <label for="login-username"><?php echo $this->msg('userlogin-yourname') ?></label>
                                                    <input type="text" class="form-control" name="username" value="" id="login-username" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="login-password"><?php echo $this->msg('userlogin-yourpassword') ?></label>
                                                    <input type="password" class="form-control" name="password" value="" id="login-password" />
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->msg('cancel') ?></button>
                                                <button type="submit" class="btn btn-primary"><?php echo $this->msg('login') ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php else : ?>
                            <div class="dropdown ml-2">
                                <button class="btn btn-primary" type="button" id="user-menu-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img class="avatar" src="<?php echo $this->data['gravatar'] ?>" width="80" height="80" />
                                    <span class="name d-xs-none d-sm-block"><?php echo $this->data['username'] ?></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <?php
                                        foreach ($this->getPersonalTools() as $key => $item) {
                                            foreach ($item['links'] as $k => $link) {
                                                if (isset($link['class'])) {
                                                    $link['class'] .= ' dropdown-item';
                                                } else {
                                                    $link['class'] = ' dropdown-item';
                                                }
                                                echo $this->makeLink( $k, $link );
                                            }
                                        }
                                    ?>
                                </div>
                            </div><!-- /.dropdown -->
                        <?php endif ?>
                        
                    </div><!-- /. -->

                    <div id="namespaces-variants" class="mb-2">
                        <!-- Tabs for talk page and language variants -->
                        <ul id="p-namespaces" class="nav nav-tabs"<?php $this->html( 'userlangattributes' ) ?>>
                            <?php foreach ($this->data['namespace_urls'] as $link) : ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo strpos($link['attributes'], 'selected') ? 'active' : '' ?>" href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
                                        <?php echo htmlspecialchars( $link['text'] ) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <?php if ($this->data['variant_urls']) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                        <?php foreach ($this->data['variant_urls'] as $link) : ?>
                                            <?php if (stripos( $link['attributes'], 'selected' ) !== false) : ?>
                                                <?php echo htmlspecialchars( $link['text'] ) ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </a>
                                    <div class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                                        <?php foreach ($this->data['variant_urls'] as $link) : ?>
                                            <a class="dropdown-item" <?php echo $link['attributes'] ?> href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>

                    <!-- Page Actions -->
                    <div id="page-actions" class="btn-toolbar float-right d-sm-none d-md-block" role="toolbar" aria-label="Toolbar with button groups">
                        <div class="btn-group btn-group-sm" role="group">
                            <?php foreach ($this->data['view_urls'] as $link) : ?>
                                <a class="btn btn-secondary" href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
                                    // $link['text'] can be undefined - bug 27764
                                if (array_key_exists( 'text', $link )) {
                                    echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
                                }
                                    ?></a>
                            <?php endforeach; ?>
                            <?php if ($this->data['action_urls']) : ?>
                                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                    <?php foreach ($this->data['action_urls'] as $link) : ?>
                                        <a class="dropdown-item" href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </header>
                <!-- /header -->

                <!-- content -->
                <div id="content" class="mw-body">
                    <a id="top"></a>
                    <div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
                    <?php if ($this->data['sitenotice']) : ?>
                    <!-- sitenotice -->
                    <div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
                    <!-- /sitenotice -->
                    <?php endif; ?>
                    <!-- firstHeading -->
                    <h1 id="firstHeading" class="firstHeading display-4 mt-0 mb-3">
                        <span dir="auto"><?php $this->html( 'title' ) ?></span>
                    </h1>
                    <!-- /firstHeading -->
                    <!-- bodyContent -->
                    <div id="bodyContent">
                        <?php if ($this->data['isarticle']) : ?>
                        <?php endif; ?>
                        <!-- subtitle -->
                        <div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
                        <!-- /subtitle -->
                        <?php if ($this->data['undelete']) : ?>
                        <!-- undelete -->
                        <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
                        <!-- /undelete -->
                        <?php endif; ?>
                        <?php if ($this->data['newtalk']) : ?>
                        <!-- newtalk -->
                        <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
                        <!-- /newtalk -->
                        <?php endif; ?>
                        <?php if ($this->data['showjumplinks']) : ?>
                        <!-- jumpto -->
                        <div id="jump-to-nav" class="mw-jump">
                            <?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
                            <a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
                        </div>
                        <!-- /jumpto -->
                        <?php endif; ?>
                        <!-- bodycontent -->
                        <?php $this->html( 'bodycontent' ) ?>
                        <!-- /bodycontent -->
                        <?php if ($this->data['printfooter']) : ?>
                        <!-- printfooter -->
                        <div class="printfooter d-none">
                            <?php $this->html( 'printfooter' ); ?>
                        </div>
                        <!-- /printfooter -->
                        <?php endif; ?>
                        <?php if ($this->data['catlinks']) : ?>
                        <!-- catlinks -->
                        <?php $this->html( 'catlinks' ); ?>
                        <!-- /catlinks -->
                        <?php endif; ?>
                        <?php if ($this->data['dataAfterContent']) : ?>
                        <!-- dataAfterContent -->
                        <?php $this->html( 'dataAfterContent' ); ?>
                        <!-- /dataAfterContent -->
                        <?php endif; ?>
                        <div class="visualClear"></div>
                        <!-- debughtml -->
                        <?php $this->html( 'debughtml' ); ?>
                        <!-- /debughtml -->
                    </div>
                    <!-- /bodyContent -->
                </div>
                <!-- /content -->

                <!-- Wiki Footer -->
                <footer class="row my-5" <?php $this->html( 'userlangattributes' ) ?>>
                    <div class="col-sm-6 text-muted">
                        <?php foreach ($this->getFooterLinks() as $category => $links) : ?>
                            <ul id="footer-<?php echo $category ?>" class="list-inline">
                                <?php foreach ($links as $link) : ?>
                                    <li id="footer-<?php echo $category ?>-<?php echo $link ?>" class="list-inline-item"><small><?php $this->html( $link ) ?></small></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php $footericons = $this->getFooterIcons("icononly");
                        if (count( $footericons ) > 0) : ?>
                            <ul id="footer-icons" class="list-inline">
                    <?php	      foreach ($footericons as $blockName => $footerIcons) : ?>
                                <li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
                    <?php	          foreach ($footerIcons as $icon) : ?>
                                    <?php echo $this->getSkin()->makeFooterIcon( $icon ); ?>

                    <?php	          endforeach; ?>
                                </li>
                    <?php	      endforeach; ?>
                            </ul>
                        <?php                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         endif; ?>
                    </div>
                </footer>

            </div><!-- /.container -->
        </div>

        <div id="toc-wrap" class="col-lg-3 col-xl-2 d-md-none d-lg-block"></div>
    </div>
</div>

<!-- Global Footer -->
<?php include(__DIR__ . '/parts/global-footer.php'); ?>
<?php $this->printTrail(); ?>

<script>
var _paq = _paq || [];
(function () {
    var u = (("https:" == document.location.protocol) ? "https://beans.opensuse.org/piwik/" : "http://beans.opensuse.org/piwik/");
    _paq.push(['setSiteId', 9]);
    _paq.push(['setTrackerUrl', u + 'piwik.php']);
    _paq.push(['trackPageView']);
    _paq.push(['setDomains', ["*.opensuse.org"]]);
    var d = document,
        g = d.createElement('script'),
        s = d.getElementsByTagName('script')[0];
    g.type = 'text/javascript';
    g.defer = true;
    g.async = true;
    g.src = u + 'piwik.js';
    s.parentNode.insertBefore(g, s);
})();
</script>

</body>
</html>
<?php
    }

    /**
     * Render a series of portals
     *
     * @param $portals array
     */
    private function renderPortals($portals)
    {
        // Force the rendering of the following portals
        if (!isset( $portals['SEARCH'] )) {
            $portals['SEARCH'] = true;
        }
        if (!isset( $portals['TOOLBOX'] )) {
            $portals['TOOLBOX'] = true;
        }
        if (!isset( $portals['LANGUAGES'] )) {
            $portals['LANGUAGES'] = true;
        }
        // Render portals
        foreach ($portals as $name => $content) {
            if ($content === false) {
                continue;
            }

            echo "\n<!-- {$name} -->\n";
            switch ($name) {
                case 'SEARCH':
                    break;
                case 'TOOLBOX':
                    $this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
                    break;
                case 'LANGUAGES':
                    if ($this->data['language_urls']) {
                        $this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
                    }
                    break;
                default:
                    $this->renderPortal( $name, $content );
                    break;
            }
            echo "\n<!-- /{$name} -->\n";
        }
    }

    private function renderPortal($name, $content, $msg = null, $hook = null)
    {
        if ($msg === null) {
            $msg = $name;
        }
        ?>
<div class="portal mb-5" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
    <h4 class="mb-3"<?php $this->html( 'userlangattributes' ) ?>><?php $msgObj = wfMessage( $msg );
    echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h4>
    <?php if (is_array( $content )) : ?>
        <ul class="list-unstyled">
            <?php foreach ($content as $key => $val) : ?>
                <?php $val['class'] = 'mb-2' ?>
                <?php echo $this->makeListItem( $key, $val ); ?>
            <?php endforeach; ?>
            <?php
            if ($hook !== null) {
                wfRunHooks( $hook, array( &$this, true ) );
            }
            ?>
        </ul>
    <?php else : ?>
        <?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
    <?php endif; ?>
</div>
<?php
    }
}

# vim:expandtab
