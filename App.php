<?php
namespace Core;
use Request\Request;


class App
{
    private array $routes = [];
//    private array $services = [];
    private LoggerServiceInterface $loggerService;
    private Container $container;

    public function __construct(LoggerServiceInterface $loggerService, Container $container)
    {
        $this->routes = [];
//        $this->services = [];
        $this->loggerService = $loggerService;
        $this->container = $container;
    }



    public function run(): void
    {
        $uri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD']; //GET; POST;

        if (array_key_exists($uri, $this->routes)) {
            $methods = $this->routes[$uri];
            if (array_key_exists($requestMethod, $methods)) {

                $handler = $methods[$requestMethod];
                $class = $handler['class'];
                $method = $handler['method'];
                $requestClass = $handler['request'];

               $objClass = $this->container->get($class);

                if (!empty($requestClass)){
                    $request = new $requestClass($uri, $requestMethod, $_POST);
                } else {
                    $request = new Request($uri, $requestMethod, $_POST);
                }


                try {
                    $objClass->$method($request);

                } catch (\Throwable $errorException) {
                    date_default_timezone_set('Asia/Irkutsk');

                    $this->loggerService->error("\n".'Произошла ошибка при обработке запроса', [
                        'message' => $errorException->getMessage(),
                        'file' => $errorException->getFile(),
                        'line' => $errorException->getLine(),
                        'time' => date('d-m-Y H:i:s')
                    ]);
                    http_response_code(500);
                    require_once './../View/500.php';
                }

            } else {
                echo "$requestMethod не поддерживается $uri";
            }

        } else {
            http_response_code(404);
            require_once './../View/404.php';
        }

    }


    public function addRoute(string $route, string $routeMethod, string $className, string $methodName, string $requestClass = null): void
    {
        $this->routes[$route][$routeMethod] = [
            'class' => $className,
            'method' =>  $methodName,
            'request' => $requestClass
        ];


    }


}
