### spider-icon
spider the website's icon by url

### Use tutorial
```php
<?php
use SpiderIcon\Spider;
Spider::request('your url');

```

### Result
![image](./20201009180311.png)
- if the website don’t have icon,will return empty array `[]`
- if the website have icon, will return array`['type'=>'url','content'=>'url']`
```
type means result type. it possible value: url and base64
content means result content. it possible value:icon's url and icon's base64
```
