<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use App\Kernel\Context\Coroutine;
use App\Kernel\Log\AppendRequestIdProcessor;
use Hyperf\Engine\Channel;
use Hyperf\Seata\Rm\PDOProxy;
use Hyperf\Seata\SqlParser\Antlr\MySql\AntlrMySQLRecognizerFactory;
use Hyperf\Utils\Context;
use HyperfTest\HttpTestCase;
use PhpParser\BuilderFactory;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\PrettyPrinter\Standard;
use PHPSQLParser\PHPSQLCreator;
use PHPSQLParser\PHPSQLParser;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileObject;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends HttpTestCase
{
    public function testExample()
    {
        $this->assertTrue(true);

        $res = $this->get('/');

        $this->assertSame(0, $res['code']);
        $this->assertSame('Hello Hyperf.', $res['data']['message']);
        $this->assertSame('GET', $res['data']['method']);
        $this->assertSame('Hyperf', $res['data']['user']);

        $res = $this->get('/', ['user' => 'limx']);

        $this->assertSame(0, $res['code']);
        $this->assertSame('limx', $res['data']['user']);

        $res = $this->post('/', [
            'user' => 'limx',
        ]);
        $this->assertSame('Hello Hyperf.', $res['data']['message']);
        $this->assertSame('POST', $res['data']['method']);
        $this->assertSame('limx', $res['data']['user']);

        Context::set(AppendRequestIdProcessor::REQUEST_ID, $id = uniqid());
        $pool = new Channel(1);
        di()->get(Coroutine::class)->create(function () use ($pool) {
            try {
                $all = Context::getContainer();
                $pool->push((array) $all);
            } catch (\Throwable $exception) {
                $pool->push(false);
            }
        });

        $data = $pool->pop();
        $this->assertIsArray($data);
        $this->assertSame($id, $data[AppendRequestIdProcessor::REQUEST_ID]);
    }

    public function testMySqlParser()
    {
        $res = (new AntlrMySQLRecognizerFactory())->create('SELECT * FROM `xxxxx` where `id` = 1;', 'mysql');
//        (new AntlrMySQLRecognizerFactory())->create('DELETE FROM `xxxxx` where `id` = 1', 'mysql');
//       $res = (new AntlrMySQLRecognizerFactory())->create('UPDATE runoob_tbl SET `runoob_title`="学习 C++" WHERE `runoob_id`=3', 'mysql');
//        $res = (new AntlrMySQLRecognizerFactory())->create('INSERT INTO `xxxxx` (`id`, `value`) VALUES(1, "v1")', 'mysql');
        dump($res);

    }

    public function testSplit()
    {
//        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
//        $code = file_get_contents('/Users/liuyuejian/code/hyperf/seata-incubator/src/SqlParser/Antlr/MySql/Parser/MySqlParser.php');
//
//        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
//        $stmts = $parser->parse($code);
//        foreach ($stmts as  $stmt) {
//            var_dump($stmt->getAttributes());
//        }
        $path = '/Users/liuyuejian/code/hyperf/seata-incubator/src/SqlParser/Antlr/MySql/Parser';
        $fqcns = array();

        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');
        foreach ($phpFiles as $phpFile) {
            $content = file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';
            $namespaceMap = [];
            for ($index = 0; isset($tokens[$index]); $index++) {
                if (!isset($tokens[$index][0])) {
                    continue;
                }
                if (T_NAMESPACE === $tokens[$index][0]) {

                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $n = $tokens[$index++][1];
                        $namespace .= $n;

                    }

                }
                if (T_CLASS === $tokens[$index][0] && T_WHITESPACE === $tokens[$index + 1][0] && T_STRING === $tokens[$index + 2][0]) {
                    $index += 2; // Skip class keyword and whitespace
                    $className = trim($tokens[$index][1]);

                    if ($className == 'MySqlLexer') {
                        continue;
                    }

                    if ($className == 'MySqlLexer' || $className == 'MySqlParser') {
                        $fqcns[] = trim($namespace).'\\'. $className;
                    } else {
                        $fqcns[] = 'Hyperf\Seata\SqlParser\Antlr\MySql\Parser\Context' . trim(trim($namespace), 'Hyperf\Seata\SqlParser\Antlr\MySql\Parser\Contex').'\\'. $className;
                    }


                    # break if you have one class per file (psr-4 compliant)
                    # otherwise you'll need to handle class constants (Foo::class)
//                    break;/**/
                }
            }
        }


        foreach ($fqcns as $fqcn) {
            $ref = new \ReflectionClass($fqcn);
            $class = '<?php';
            var_dump('============');
            var_dump($ref->getStartLine(), $ref->getEndLine());

            $class .= PHP_EOL . 'namespace ' . $ref->getNamespaceName() . ';' . PHP_EOL;

            $class .= 'use Antlr\Antlr4\Runtime\ParserRuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
	use Antlr\Antlr4\Runtime\Tree\TerminalNode;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
	use Hyperf\Seata\SqlParser\Antlr\MySql\Parser\MySqlParser;
	use Hyperf\Seata\SqlParser\Antlr\MySql\Listener\MySqlParserListener;';
            $class .= $this->getFileLines('/Users/liuyuejian/code/hyperf/seata-incubator/src/SqlParser/Antlr/MySql/Parser/MySqlParser.php' ,$ref->getStartLine(), $ref->getEndLine());
//            $namespace = $ref->getNamespaceName();
//
//            $class .=  PHP_EOL . 'namespace ' . $namespace . ';';
//

//
//
//
//            $class .=  PHP_EOL . 'class ' . $className . '{';
//
//            foreach ($ref->getMethods() as $method) {

//            }
////           var_dump('-----');
//
//            $class .= '}';
//
////            var_dump($class);
///
            $className = $ref->getName();
            $classMap = explode('\\', $className);
            $className = $classMap[array_key_last($classMap)];
//            var_dump($className);
            file_put_contents('/Users/liuyuejian/code/hyperf/seata-incubator/src/SqlParser/Antlr/MySql/Parser/Context/' . $className . '.php', $class);
        }
    }

    /** 返回文件从X行到Y行的内容(支持php5、php4)

     * @param string $filename 文件名

     * @param int $startLine 开始的行数

     * @param int $endLine 结束的行数

     * @return string

     */

    function getFileLines($filename, $startLine = 1, $endLine=50, $method='rb') {
        $content = '';

        $count = $endLine - $startLine;

// 判断php版本(因为要用到SplFileObject，PHP>=5.1.0)

        $fp = new SplFileObject($filename, $method);

        $fp->seek($startLine-1);// 转到第N行, seek方法参数从0开始计数

        for($i = 0; $i <= $count; ++$i) {
            $content .=$fp->current();// current()获取当前行内容

            $fp->next();// 下一行

        }


     return $content; // array_filter过滤：false,null,''

    }

    public function testPrepare()
    {
        $refClass = new \ReflectionClass(PDOProxy::class);
        /** @var PDOProxy $pdoProxy */
//        $pdoProxy =  $refClass->newInstanceWithoutConstructor();
        $parser = new PHPSQLParser();
//        $sql = 'SELECT * FROM testTable_1 as t1 where id in (1,2,3,4,5) FOR UPDATE ';
//        $parse = $parser->parse($sql);
//        $sql = 'UPDATE testTables as tt set a="A" where id in (select id from test_tables as subTT)';
//        $sql = 'INSERT INTO testTable (a,b,c) VALUES ("A","B","C"),("A","B","C")';
        $sql = 'DELETE FROM testTable_1 as t1 where id in (1,2,3,4)';

        $parse = $parser->parse($sql);
        unset($parse['DELETE']);
        $newParse = [];
        $newParse['SELECT'][] = [
            "expr_type" => 'colref',
            "alias" => false,
            "base_expr" => '*',
            "sub_tree" => false,
            "delim" => false,
        ];

        foreach ($parse as $k => $item) {
            $newParse[$k] = $item;
        }
        $creator = new PHPSQLCreator();
        $res = $creator->create($newParse);
//        $res = $pdoProxy->prepare($sql);
        $res .= ' FOR UPDATE';
        dump($res);

    }



}
