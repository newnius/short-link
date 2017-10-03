# short-link
shorten long links

## features
#### shorten long url
    api.php?action=set&url=http://blog.newnius.com

#### shorten long url with selected short url
    api.php?action=set&url=http://blog.newnius.com&token=newnius

#### query origin long url
    api.php?action=get&token=newnius

#### analysis of visitors visiting short url

#### rate control
  - use [<code>RateController</code>](http://github.com/newnius/util-php/RateController.php) module to control rate

## TODO
  - Add `Claim`
	- ReDesign redis keys
	- Enable Admin support
	- Analytics support
