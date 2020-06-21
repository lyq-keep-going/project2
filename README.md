# project2说明文档
姓名：罗宇琦 

学号：18307130255

### 项目完成情况

本次project中，我完成了所有基本功能。

**首页**

关于登录和个人中心的显示，由于前后端是分离的，使用token作为登录的凭证，所以我通过判断前端是否存有token来进行v-if条件渲染。

渲染热门图片，我会渲染5张最热门的图片。做这里的时候对sql语句运用不熟练，我主要是用php处理了从sql取出来的数据。实现比较难看，但是功能正确。

传参数的时候有一个参数表明是第一次访问还是刷新访问，可以获得不同条件下筛选的图片。

**浏览页**

浏览页主要是对查询的一个考察，模糊查询主要是考察这个语句

```php
$query = 'SELECT PATH,ImageID FROM travelimage WHERE Title LIKE ?';
```

其他的查询比较简单，然后是热门内容、城市、国家的选择，我是以在travelimage里出现次数排列的

content很简单，主要是city和country，因为只能查到code，所以得做一个转化

```php
$query = 'SELECT CityCode ,COUNT(UID)
FROM travelimage
GROUP BY CityCode
ORDER BY COUNT(UID) DESC
LIMIT 0,6';

$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$hotCities = array();

while($row = mysqli_fetch_assoc($result)){
    $hotCities[] = codeToCity($row['CityCode']);
}
```

由于涉及多种搜索，这里的分页我是用的前端分页。

**搜索页**

搜索页和浏览页相比就很简单了，点击跳转图片我使用了ImageID。一开始我选择的是PATH，但是后来意识到PATH有可能不同，全部改成ImageID。（后来删除上传图片的时候意识到PATH必须唯一，又在上传文件名后加了一个时间字符串保证唯一性）

```html
outerDiv.innerHTML = ' <a onclick="enterPictureDetails(this.dataset.imageID)"></a>';
                let a = outerDiv.firstElementChild;
                a.dataset.imageID = item.ImageID;
```

**上传页面**

 合法性校验主要使用v-model，然后再提交时判断合法性

对已上传图片修改，图片部分我仅展示图片，文件没有发送到前端。在提交时，如果用户没有上传文件则不放img这个字段。在后端用isset进行判断，进行两种操作。

主题部分的select使用的时vue的列表渲染，country是onload是后端返回的，country的值有所改变时请求后端返回cities

**我的照片**

对myphoto数组进行判断，条件渲染提示没有照片时的文字

点击修改，将ImageID存在localstorage

删除图片：删除travelimage中记录和travelimagefavor中记录

分页时，使用后端分页。后端用LIMIT ORBER BY查询，前端给page信息。

**我的收藏**

实现方法与我的照片非常类似，不做赘述

**登录**

登录时，后端验证前端用户名和密码输入正确后会给一个token

```php
 $dateNow = date('U');
    $payload = array(
        "iss"=>"http://".HOST,
        "aud"=>"http://".HOST,
        "iat"=>$dateNow,
        "nbf"=>$dateNow,
        "exp"=>$dateNow + 3600*5,//五小时过期
        "username"=>$username,
        "password"=>$password
    );
    $jwt = JWT::encode($payload, $key);

    https(200);
    echo json_encode(array('message'=>'Successful login!','token'=>$jwt,'username'=>$username));
```

**注册**

使用v-model对数据读取和验证，规定密码需字母和数字，用户名需不超过20个字符。后端对密码加盐，这部分在bonus中提及。

**详细图片**

可以实现功能，实现技术已被上述内容包括。



### Bonus完成情况

**哈希加盐**

经过我的了解，哈希加盐主要是为了防止数据库泄露时，密码被不法分子盗取。如果只是单纯的对密码进行hash，由于密码一般不长，经过穷举或者查表的方法可以获得一部分密码，因而不是很安全。而在密码后加一段比较长的随机字符串（盐），这样通过查表或穷举的方法就很难得知真正的密码。而且由于盐的长度是不固定的，使密码更难破解。

理论上来说盐应该存在另一个服务器上，但不存在这个条件，这里简便起见我就存在traveluser里面了。

```php
function generateSalt(){
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );

    $length = mt_rand(50,70);
    $charsLen = count($chars) - 1;
    shuffle($chars);                            //打乱数组顺序
    $salt = '';
    for($i=0; $i<$length; $i++){
        $salt .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
    }

    return $salt;
}

function addSalt($password,$salt){
    return sha1(sha1($password).$salt);
}
```

**使用现代前端框架**

本次项目中我使用了vue框架。

vue确实是充分考虑了前端需求后出现的框架，很多前端常用的技术用vue写回方便很多。比如v-model将input标签中的内容动态绑定在某个变量上，比如v-if条件渲染，v-for列表渲染等等都大大方便了前端的操作。还有v-bind动态绑定数据，可以动态控制标签属性。还有{{}}插值语句对于动态渲染也非常好用。

```html
div class="register">
        <label>用户名</label>
        <input type="text" v-model="username">
        <label>邮箱</label>
        <input type="text" v-model="mailbox">
        <label>密码</label>
        <input type="password" v-model="password">
        <label>确认密码</label>
        <input type="password" v-model="rePassword">
        <span class="warning">{{warning}}</span>
        <button type="button" v-on:click="register()">Register</button>
    </div>
```

↓条件渲染

```html
<div class="dropdown" v-if="haveToken">
                <img src="https://cube.elemecdn.com/3/7c/3ea6beec64369c2642b92c6726f1epng.png" height="60px" width="60px">
                <div class="dropdown-content">
                    <a href="upload.html"><i class="fa fa-arrow-circle-up" fa-2x aria-hidden="true"></i>&nbsp上传</a>
                    <a href="myPhotos.html"><i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp我的照片</a>
                    <a href="favourites.html"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp我的收藏</a>
                    <a onclick="logout()"><i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp登出</a>
                </div>
            </div>
            <span v-if="!haveToken"><a href="login.html"  class="login" >login</a></span>
```

```html
<div class="pages">
        <a @click="pageSub()">上一页</a>
        <ul id="page-num">
            <a v-if="page-2>0" @click="getNewPage(page-2)"><li>{{page-2}}</li></a>
            <a v-if="page-1>0" @click="getNewPage(page-1)"><li>{{page-1}}</li></a>
            <a><li class="red">{{page}}</li></a>
            <a v-if="page+1<=totalPage" @click="getNewPage(page+1)"><li>{{page+1}}</li></a>
            <a v-if="page+2<=totalPage" @click="getNewPage(page+2)"><li>{{page+2}}</li></a>
        </ul>
        <a @click="pageAdd()">下一页</a>

    </div>
```

vue有一个非常好用的功能，可以设置模板，但是我在这个直接引入的环境下不是很会使用（之前软工是在vue-cli环境下使用），最终对于重复的组件还是用的js循环语句生成了。然后beforeCreated这个钩子函数，在这种环境下的行为我也不是很理解，所以直接用onload的了。发送请求我用的axios方法。

