## 瞎叨叨

1. 本框架仅限于个人练手框架，没有严格的测试，可能会有很多 bug，别把此项目拿去写产品，后果自负哈      
2. 所有的数据库操作均使用原生态 sql 语句操作，因此会出现 sql 注入的风险，需要注意   
3. 本项目仅自己用于尝试写 mvc 架构，仅单纯提供学习资源，别当作圣经，也别恶意吐槽，毕竟我也不是什么大神  
4. 关于作者，本来写的也很菜，就不留名了。框架核心目录为 `Sugaryesp` 也是我的网名 `削个椰子皮_给个梨`  
5. 这个只是自己一时兴起而作，后期没有打算继续维护下去了，毕竟现在 `php` 的一些 `web` 框架已经足够优秀了，没有必要重复造轮子  
6. 祝大家用的开森，谢谢！~~~///(^v^)\\\~~~  

## 功能点
1. 支持 MVC 模式
2. 支持自定义路由（通过 url 传参的方式决定路由访问）
3. 支持多配置文件（配置文件支持点语法读取）
4. 支持原生 DB 写法
5. 支持简易的 ORM 模型
6. 支持自定义助手函数
7. 支持中间件
8. 支持模型观察者

## 项目访问

- 直接将虚拟域名 `your_domain` 指向项目根目录下，然后浏览器访问 `your_domain` 出现以下内容，即表示项目运行成功，默认访问的控制器方法路径为 `App\Controller\Home::class@index` （项目根目录下的 `index.php` 为此项目的入口文件）

> hello world
>
> writed by Alex
>

- 配置数据库

复制项目根目录下的 `.env.example` 文件为 `.env` 配置以下数据库配置为自己的数据库配置即可

> 目前仅支持  `mysql` 数据库，且采用  `mysqli` 扩展库进行操作数据库

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

> 需要注意的是：在 `.env` 配置文件中，允许添加注释，但需要使用 `#` 号开头，eg： #数据库连接信息，但一定需要注意配置值不可带有 `#` 号，否则会认为是注释信息   

-[] DB_PASSWORD=123456   正确  
-[x] DB_PASSWORD=123#456   错误，此时数据库的密码会认为是 123

## 目录结构

- App 应用逻辑目录
    - Controller 控制器目录
        - Home.php  首页控制器
    - Middleware 控制器中间件目录（装饰器）
        - Json.php 返回 json 格式的装饰器
        - Login.php 判断账户是否登陆的装饰器（中间件）
        - MiddlewareDecorator.php 装饰器接口类
        - Template.php 模板渲染装饰器
    - Model 模型目录
    - Observer 模型观察者目录
        - Observer.php 观察者接口类
- config 配置文件目录
    - app.php  应用配置
    - controller.php 控制器相关配置
    - database.php 数据库相关配置
    - model.php 模型相关配置
- doc 文档相关目录
    - readme.md 框架简要文档    
- public 公共目录
    - storage 资源目录
- resource 静态资源文件目录
    - css css 目录
    - js js目录
- Sugaryesp 框架核心目录
    - Library 工具目录
        - Database 数据库相关目录
            - Database.php 实际项目应用的数据库扩展类
            - IDatabase.php 数据库操作接口类
            - MySQLi.php mysqli 数据库扩展封装
            - PDO.php pdo 数据库扩展封装
        - Config.php 封装读取配置文件类
        - Controller.php 控制器基类
        - DB.php 数据库操作基类
        - Env.php 环境变量操作基类
        - Factory.php 工厂类
        - helper.php 助手函数
        - Model.php 模型基类
        - Register.php 注册类
        - Request.php 请求类
        - VarDumper.php 调试类
    - App.php 应用初始类
    - Base.php 常量初始文件
    - Loader.php 自动加载类
- templates 模板目录
- .env 配置文件
- .env.example 配置文件备份
- .gitignore git 忽略文件配置
- index.php 应用入口文件    

## Controller 相关

- 所有的控制器需要写到 `App\Controller` 目录下，且`不支持`多层级结构
- 所有的控制器`必须`继承 `Sugaryesp\Library\Controller` 类，不然无法加载模板

1、手动渲染模板 

```
<?php

namespace App\Controller;

use Sugaryesp\Library\Controller;
use Sugaryesp\Library\Config;

class Home extends Controller
{

    public  function index()
    {
        $helloWord = Config::get('controller')['default'];
        $this->assign('helloWord', $helloWord);
        $this->display();

        // 或者可以直接指定模板文件
        // $this->display('home/index');
    }

}
```

2、 自动渲染模板

> 当 `url` 传参 `app=json` 时，返回 `json` 数据，当 `app=html` 时，返回默认模板，控制器中可以直接返回数组

```php

    // /index.php?c=Home&app=json  => 返回 json 数据
    // /index.php?c=Home&app=html  => 返回模板

    public function index()
    {
        $name = 'Alex';
        $age = 18;
        return compact('name', 'age');
    }

```

## View 相关

- 模板文件需要写到 `templates/` 目录下，以 `类名` 小写名称为文件夹， `控制器方法名` 为文件名， 比如 `home/index.php`  （默认情况下）
> 当需要在控制器中特定指定模板文件时，模板文件的写法按照自己指定的路径来写

## Model 相关

#### 直接操作 `DB` 类时，需要引用 `use Sugaryesp\Library\DB;` 

- 查询语句时，支持链式语法

```php

    public function getList()
    {
        $data = DB::table('contact_information')
//            ->fields()  // *
//            ->fields('id,contact_name')  // id,contact_name
            ->fields(['id', 'contact_name'])
//            ->where('id', 10)  // id = 10
            ->where('id', '>=', 0)  // id >= 0
//            ->whereIn('id', '10,11,12')  // where id in (10,11,12)
            ->whereIn('id', [10, 11, 12])
//            ->whereNotIn('id', '3,4,5')  // where id not in (3,4,5)
            ->whereNotIn('id', [3, 4, 5])
//            ->group('id,contact_name')  // group by id,contact_name
            ->group('id')
            ->group('contact_name')
            ->order('id')  // order by id desc
            ->order('updated_at', 'asc')  // order by updated_at asc
//            ->limit()  // limit 0,20
//            ->limit(5)  // limit 0,5
            ->limit(2,3)  // limit 2,3
            ->select();
        
        var_dump($data);
        
    }

```

- 按主键查找数据，返回一条数据

```php

    $result = DB::table('contact_information')->find(1); 

```

- 新增语句时，返回新增记录的 id 值

```php

    public function add()
    {
        $input = [
            'league' => '加盟商' . mt_rand(1, 100),
            'contact_name' => '联系人' . mt_rand(1, 100),
            'contact_email' => mt_rand(100000000, 999999999) . '@qq.com',
            'contact_phone' => '155' . mt_rand(10000000, 99999999),
            'sort' => mt_rand(1, 10000),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // 插入成功后的记录 id
        $id = DB::table('contact_information')->insert($input);
    }

```

- 更新语句时，返回影响的行数

```php

    public function modify()
    {
        $input = [
            'league' => '加盟商' . mt_rand(1, 100),
            'contact_name' => '联系人' . mt_rand(1, 100),
            'contact_email' => mt_rand(100000000, 999999999) . '@qq.com',
            'contact_phone' => '155' . mt_rand(10000000, 99999999),
            'sort' => mt_rand(1, 10000),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // 返回影响的行数
        $affectedRows = DB::table('contact_information')->where('id', '>=', 40)->update($input);
    }

```

- 删除语句时，返回影响的行数

```php

    public function del()
    {
        $affectedRows = DB::table('contact_information')->delete();  // 为了防止全表删除，因此不给 delete 传参时，会直接报错
        $affectedRows = DB::table('contact_information')->delete(40);
        $affectedRows = DB::table('contact_information')->delete([40, 41]);
    }

```

- 执行原生 `sql` 语句时

> 注意：查询语句时，必须设定 `execute` 方法中的参数为 `true` ，不然不会出来结果值 

```php

    $result = DB::table()->draw('insert into contact_information ( `league`,`contact_name`,`created_at`,`updated_at` ) values ( "加盟商39","联系人76","2020-08-18 15:53:44","2020-08-18 15:53:44" )')->execute();  // 返回 true 或 false
    $result = DB::table()->draw('delete from contact_information where `id` = 6')->execute();  // 返回 true 或 false
    $result = DB::table()->draw('update contact_information set `league` = "加盟商23" ,`contact_name` = "联系人41" where `id` = 1')->execute();  // 返回 true 或 false
    $result = DB::table()->draw('select * from contact_information')->execute(true);  // 返回 false 或者 数据数组
```

- 获取当前执行的 `sql` 语句，直接在 `select()` 或 `update()` 或 `insert()` 或 `delete()` 方法前调用 `fetchSql()` 链式语法

```php

        $input = [
            'league' => '加盟商' . mt_rand(1, 100),
            'contact_name' => '联系人' . mt_rand(1, 100),
            'contact_email' => mt_rand(100000000, 999999999) . '@qq.com',
            'contact_phone' => '155' . mt_rand(10000000, 99999999),
            'sort' => mt_rand(1, 10000),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $result = DB::table('contact_information')->fetchSql()->insert($input);
        $result = DB::table('contact_information')->fetchSql()->delete([40, 41]);
        $result = DB::table('contact_information')->where('id', '>=', 40)->fetchSql()->update($input);
        $result = DB::table('contact_information')->fetchSql()->select();
        $result = DB::table('contact_information')->fetchSql()->find(1);

        $result = DB::table()->draw('insert into contact_information ( `league`,`contact_name`,`created_at`,`updated_at` ) values ( "加盟商39","联系人76","2020-08-18 15:53:44","2020-08-18 15:53:44" )')->fetchSql()->execute();
        $result = DB::table()->draw('delete from contact_information where `id` = 6')->fetchSql()->execute();
        $result = DB::table()->draw('update contact_information set `league` = "加盟商23" ,`contact_name` = "联系人41" where `id` = 1')->fetchSql()->execute();
        $result = DB::table()->draw('select * from contact_information')->fetchSql()->execute();
```

#### 使用模型的 `orm` 时
- 所有的模型需要写到 `App\Model` 目录下，且`不支持`多层级结构
- 必须继承 `Sugaryesp\Library\Model` 类 

```php

<?php

namespace App\Model;

use Sugaryesp\Library\Model;

class ContactInformation extends Model
{

    /**
     * 是否需要自动写入时间戳
     * 
     * @var bool true|false
     */
    protected $autoWriteTimestamp = true;

    /**
     * 指定表名，如果不指定，则按照模型类的蛇形命名规则
     * eg : class is ContactInformation => table is contact_information
     * 
     * @var string 
     */
    protected $table = 'my_table';

    /**
     * 指定创建时间字段，默认为 created_at
     * 
     * @var string 
     */
    protected $createTime = 'c_time';

    /**
     * 指定更新时间字段，默认为 updated_at
     * 
     * @var string 
     */
    protected $updateTime = 'u_time';

    /**
     * 指定变动字段，需要注意的是必须要含有主键字段，否则无法更新
     * 
     * @var string[] 
     */
    protected $fillable = ['id', 'name', 'age',];

}

```

- 新增数据时 （默认均会写入 `created_at` 和 `updated_at` 字段，除非关闭时间字段自动写入）

1、 以对象属性赋值方式新增数据时 `save()` 方法

```php

//        $model3 = new ContactInformationModel();
//        $model3 = Factory::getModel('ContactInformation');
        $model3 = model('ContactInformation');

        $model3->name = '加盟商' . mt_rand(1, 100);
        $model3->age = mt_rand(10, 999);
        $res = $model3->save(); // 新增数据的 id

```

2、以数组方式新增数据时 `fill()` 方法和 `save()` 方法

```php

//        $model3 = new ContactInformationModel();
//        $model3 = Factory::getModel('ContactInformation');
        $model3 = model('ContactInformation');
        $input = [
            'name' => '加盟上003',
            'age' => 198,
            'aa' => 456,
            'bb' => 36,
        ];
        // 必须调用 fill() 方法，进行过滤指定字段
        $model3->fill($input);
        $res = $model3->save(); //  新增数据的 id

```

3、以数组的方式新增数据时 `create()` 方法

> 注意：`create()` 方法不会过滤数据表的字段，因此一定要注意不要将 `主键` 字段放到 `$input` 数组中，否则主键也会按照 `$input` 数组的规则新增

```php

        $model3 = model('ContactInformation');
        $input = [
            'name' => '加盟上003',
            'age' => 198,
//            'aa' => 456,  // 数据表中不具备的字段，不要写进去
//            'bb' => 36,
//            'id' => 40  // 主键字段不要写进去，除非特殊业务需求
        ];

        $res = $model3->create($input);  // 返回新增数据的  id

```

- 更新数据时

1、 以对象属性赋值方式更新数据时 `save()` 方法

```php

        $model3 = new ContactInformationModel(40);
        $model3->name = '张三001';
        $model3->age = 789;
        $model3->id = 79;  // 如果在此处指定主键 id，那么此时将会以此处的 id 为准，如果不指定，则更新 id = 40 的数据，指定则更新 id = 79 的数据
        $res = $model3->save(); //  返回影响的行数

// 或者直接使用以下的方式
//        $model3 = Factory::getModel('ContactInformation');
        $model3 = model('ContactInformation');
        $model3->name = '张三001';
        $model3->age = 789;
        $model3->id = 79;  // 直接指定了主键 id

```

2、以数组方式更新数据时 `fill()` 方法和 `save()` 方法

```php

//        $model3 = Factory::getModel('ContactInformation');
        $model3 = model('ContactInformation');
//        $model3 = new ContactInformationModel();
        $input = [
            'name' => '加盟上' . mt_rand(1, 100),
            'age' => mt_rand(1, 100),
            'aa' => 456,
            'bb' => 36,
            'id' => 40,  // 需要在此处指定主键 id 的值
        ];

        $model3->fill($input);

        $res = $model3->save(); //  返回影响的行数

```

3、以数组的方式更新数据时 `modify()` 方法

> 注意：`modify()` 方法不会过滤数据表的字段，因此一定要注意不要将 `主键` 字段放到 `$input` 数组中，否则主键也会按照 `$input` 数组的规则更新，并且一定需要写 `where` 语句

```php

//        $model3 = Factory::getModel('ContactInformation');
        $model3 = model('ContactInformation');
//        $model3 = new ContactInformationModel();
        $input = [
            'name' => '加盟上' . mt_rand(1, 100),
            'age' => mt_rand(1, 100),
//            'aa' => 456,
//            'bb' => 36,  // 也不能写数据表之外的字段
//            'id' => 40,  // 此处一定不要写主键 id
        ];

        $res = $model3->where('id', '>=',7)->modify($input);  // 返回影响数据的行数

```

- 查询数据时

1、 根据主键查询数据时

```php

        $model3 = new ContactInformationModel(3);
        var_dump($model3->name);  // 可以直接获取数据表字段值

```

2、 按条件查询数据时 （支持所有的 DB 类的查询方法）

```php

//        $model3 = Factory::getModel('ContactInformation');
//        $model3 = model('ContactInformation');
        $model3 = new ContactInformationModel();

        $res = $model3->where('id', '>=', 0)->select();

        var_dump($res);die;

```

- 删除数据时

1、 按条件删除数据时 （支持 DB 类的 `delete()` 方法）

```php

//        $model3 = Factory::getModel('ContactInformation');
        $model3 = model('ContactInformation');
//        $model3 = new ContactInformationModel();

//        $res = $model3->where('id', '>=', 0)->fetchSql()->delete();
        $res = $model3->fetchSql()->delete([1,2,3]);

        var_dump($res);die;

```