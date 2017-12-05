<?php

namespace PETest\Component\Asset\Filter;

use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Filter\CSSRewriteFilter;

class CSSRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $rootURI;

    /**
     * @var string
     */
    private $rootDIR;

    protected function setUp()
    {
        $this->rootURI = 'http://example.com';
        $this->rootDIR = __DIR__ . '/../TestAsset/';
    }

    public function testFilter()
    {
        $expected = <<<EOF
/*this is local stylesheet*/
@import 'http://example.com/global.css';
@import url(http://example.com/src/relative.css);
@import url(http://foo.com/external.css);
* {
    background-image: url(http://example.com/src/dir/relative.jpg);
}
a {
    background-image: url(http://example.com/root.jpg);
}
i {
    background-image: url(data:image/gif;base64,...);
}
img {
    filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='http://example.com/workshop/graphics/earglobe.gif',sizingMethod='scale');
}
EOF;

        $source = new LocalAsset('local.css');
        $source->setRootURI($this->rootURI . '/src');
        $source->setRootDIR($this->rootDIR);

        $target = new LocalAsset('filtered.css');
        $target->setRootURI($this->rootURI . '/dist');
        $target->setRootDIR($this->rootDIR);

        $filter = new CSSRewriteFilter();
        $filter->filter($source, $target);

        static::assertSame($expected, $source->getContent());
    }
}
