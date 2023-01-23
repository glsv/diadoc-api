# Назначение
Обертка для работы с API [api-docs.diadoc.ru](https://api-docs.diadoc.ru/).
- Осуществляет запросы к API с самостоятельным получение и хранением токена по логину и паролю;
- Формализует использование через работу с абстракциями команд и результатов выполнения оформленных в виде DTO;

## Установка

```shell
composer require glsv/diadoc-api
```

## Зависимости
- PHP 7.4+
- [guzzlehttp/guzzle](https://github.com/guzzle/guzzle/)

## Использование
### Общая логика работы с библиотекой
```
// Сервис аутентификации
$authenticator = new PasswordAuthenticator($login, $passwd);

// API с передачей в него настрок и зависимостей
$api = new DiadocClientApi($baseUrl, $developer_key, $authenticator);

// Сформировать запрос на конкретную функцию. 
// Инициализация всех обязательных свойств запроса производится через конструктор
$request = new GetDocumentRequest($boxId, $messageId, $entityId);

// Создать объект команды с передачей API и созданного запроса
$command = new GetDocumentCommand($api, $request);

// Выполнить команду
$result = $command->execute();

```
В $result будет возвращен объект, реализующий интерфейс `Glsv\DiadocApi\interfaces\ApiResponseInterface`

ApiResponseInterface реализует 3 метода:
- isError(): bool
- getError(): string
- getData(): array

Таким образом, можно через методы проверить успешно ли прошло выполнение запроса.
Если нет, то получить сообщение об ошибке.

Если запрос выполнен успешно, то получить результаты методом `getData()`. 
Содержимое `getData()` зависит от типа запроса. Формат возвращаемых данных требуется 
смотреть в документации к Diadoc.  

### 1. Получение документа (метаданные)
```
<?php
use Glsv\DiadocApi\services\PasswordAuthenticator;
use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\requests\GetDocumentRequest;
use Glsv\DiadocApi\commands\GetDocumentCommand;

$baseUrl = 'https://diadoc-api.kontur.ru';
// Your login and passwd in diadoc.ru
$login = 'user@yourdomain.com';
$passwd = 'passwd';
$developer_key = 'gl-a05289f6-408f-4ece-9670-xxxxxx';

$boxId = '4fb9ac6ec32e40579a106fdb0092aea6';
$messageId = 'dc7e1be3-4d22-4c6b-89ce-6df3186e1c56';
$documenId = 'ec164dfb-bc95-442f-8f60-e34ee51e5c16';

$authenticator = new PasswordAuthenticator($login, $passwd);
$api = new DiadocClientApi($baseUrl, $developer_key, $authenticator);

$request = new GetDocumentRequest($boxId, $messageId, $entityId);
$res = (new GetDocumentCommand($api, $request))->execute();

print_r($res);
```
#### Результаты
В качестве результата возвращается объект `SuccessResponse` или `ErrorResponse`, имплементирующие интерфейс
`Glsv\DiadocApi\interfaces\ApiResponseInterface`.
Сырые данные ответа Diadoc могут быть получены в виде массива методом `getData()` данных объектов.

Формат возвращамых данных от Diadoc: [developer.kontur.ru/Docs/diadoc-api/proto/Document.html](https://developer.kontur.ru/Docs/diadoc-api/proto/Document.html)

```
Glsv\DiadocApi\responses\SuccessResponse Object
(
    [data:protected] => Array
        (
            [MessageId] => dc7e1be3-4d22-4c6b-89ce-6df3186e1c57
            [EntityId] => ec164dfb-bc95-442f-8f60-e34ee51e5c15
            [CreationTimestampTicks] => 638060933238601247
            [CounteragentBoxId] => 51082e19b28d4ffcb0f974431444ae03@diadoc.ru
            [DocumentType] => AcceptanceCertificate
            
            /////////////////////////
            // Другие атрибуты response
            /////////////////////////
            
            [DocflowStatus] => Array
                (
                    [PrimaryStatus] => Array
                        (
                            [Severity] => Success
                            [StatusText] => Подписан контрагентом
                        )

                )

            [MessageIdGuid] => dc7e1be3-4d22-4c6b-89ce-6df3186e1c57
            [EntityIdGuid] => ec164dfb-bc95-442f-8f60-e34ee51e5c15
            [CreationTimestamp] => 2022-12-08T10:48:43.8601247Z
        )
)

```

### 2. Получение печатной подписанной формы документа
```
<?php
use Glsv\DiadocApi\services\PasswordAuthenticator;
use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\requests\GeneratePrintFormRequest;
use Glsv\DiadocApi\commands\GeneratePrintFormCommand;

$baseUrl = 'https://diadoc-api.kontur.ru';
// Your login and passwd in diadoc.ru
$login = 'user@yourdomain.com';
$passwd = 'passwd';
$developer_key = 'gl-a05289f6-408f-4ece-9670-xxxxxx';

$boxId = '4fb9ac6ec32e40579a106fdb0092aea6';
$messageId = 'dc7e1be3-4d22-4c6b-89ce-6df3186e1c56';
$documenId = 'ec164dfb-bc95-442f-8f60-e34ee51e5c16';

$authenticator = new PasswordAuthenticator($login, $passwd);
$api = new DiadocClientApi($baseUrl, $developer_key, $authenticator);

$request = new GeneratePrintFormRequest($boxId, $messageId, $entityId);
$res = (new GeneratePrintFormCommand($api, $request))->execute();

print_r($res);
```
#### Результаты
В качестве результата возвращается объект `SuccessFileResponse` или `ErrorResponse`, имплементирующие интерфейс
`Glsv\DiadocApi\interfaces\ApiResponseInterface`.

При успешном выполнении SuccessFileResponse->getData() возвращает массив в одним элементом `FileDto()`
`FileDto` содержит содержимое файлы в base64, название и contentType.

```
Glsv\DiadocApi\responses\SuccessFileResponse Object
(
    [fileDto:protected] => Glsv\DiadocApi\dto\FileDto Object
        (
            [binaryData] => Содержимое файла в base64
            [filename] => 4fb9ac6e-c32e-4057-9a10-6fdb0092aea5.ec164dfb-bc95-442f-8f60-e34ee51e5c15.638061937254976253_ru_.pdf
            [contentType] => application/pdf
        )
)

```
