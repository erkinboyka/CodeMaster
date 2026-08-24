<?php

$startTs1 = "interface User {\n  name: string;\n  age: number;\n  email?: string;\n}\n\nconst users: User[] = [\n  { name: 'Alice', age: 25 },\n  { name: 'Bob', age: 30 }\n];\n\nfunction findUser(name: string): User | undefined {\n  return users.find(u => u.name === name);\n}";

$startTs2 = "type StringOrNumber = string | number;\n\nfunction process(value: StringOrNumber): string {\n  if (typeof value === 'string') {\n    return value.toUpperCase();\n  }\n  return String(value);\n}";

$startTs3 = "function merge<T extends object, U extends object>(a: T, b: U): T & U {\n  return { ...a, ...b };\n}\n\nconst result = merge({ name: 'Alice' }, { age: 25 });";

$startTs4 = "type UserRole = 'admin' | 'user' | 'moderator';\n\ninterface User {\n  name: string;\n  role: UserRole;\n  permissions: string[];\n}\n\nfunction hasPermission(user: User, perm: string): boolean {\n  return user.permissions.includes(perm);\n}";

$startTsDecorator = "function Log(target: any, key: string, descriptor: PropertyDescriptor) {\n  const original = descriptor.value;\n  descriptor.value = function(...args: any[]) {\n    console.log('Calling ' + key + ' with', args);\n    return original.apply(this, args);\n  };\n  return descriptor;\n}\n\nclass Calculator {\n  @Log\n  add(a: number, b: number) {\n    return a + b;\n  }\n}";

$startDockerCmds = "docker pull nginx:latest\ndocker run -d --name web -p 8080:80 nginx:latest\ndocker ps\ndocker logs web";

$startDockerfile16 = 'FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --production
COPY . .
EXPOSE 3000
CMD ["node", "server.js"]';

$startCompose16 = "version: '3.8'\nservices:\n  web:\n    build: .\n    ports:\n      - '3000:3000'\n  db:\n    image: postgres:15\n    environment:\n      POSTGRES_PASSWORD: secret";

$startK8sPod = '
apiVersion: v1
kind: Pod
metadata:
  name: my-pod
spec:
  containers:
  - name: app
    image: nginx:latest
    ports:
    - containerPort: 80
';

$startK8sDeploy = '
apiVersion: apps/v1
kind: Deployment
metadata:
  name: my-deployment
spec:
  replicas: 3
  selector:
    matchLabels:
      app: my-app
  template:
    metadata:
      labels:
        app: my-app
    spec:
      containers:
      - name: app
        image: nginx:latest
';

$startK8sService = '
apiVersion: v1
kind: Service
metadata:
  name: my-service
spec:
  selector:
    app: my-app
  ports:
  - port: 80
    targetPort: 8080
  type: LoadBalancer
';

$startK8sConfig = '
apiVersion: v1
kind: ConfigMap
metadata:
  name: app-config
data:
  DATABASE_URL: "postgres://db:5432/mydb"
  API_KEY: "my-secret-key"
';

$startMobile2 = "import React from 'react';\nimport { View, Text, StyleSheet } from 'react-native';\n\nconst App = () => {\n  return (\n    <View style={styles.container}>\n      <Text style={styles.title}>Hello World</Text>\n    </View>\n  );\n};\n\nconst styles = StyleSheet.create({\n  container: { flex: 1, justifyContent: 'center', alignItems: 'center' },\n  title: { fontSize: 24, fontWeight: 'bold' }\n});";

$startMobileNav = "import { NavigationContainer } from '@react-navigation/native';\nimport { createStackNavigator } from '@react-navigation/stack';\n\nconst Stack = createStackNavigator();\n\nfunction App() {\n  return (\n    <NavigationContainer>\n      <Stack.Navigator>\n        <Stack.Screen name='Home' component={HomeScreen} />\n        <Stack.Screen name='Details' component={DetailsScreen} />\n      </Stack.Navigator>\n    </NavigationContainer>\n  );\n}";

$startMobileApi = "const fetchUsers = async () => {\n  try {\n    const response = await fetch('https://api.example.com/users');\n    const data = await response.json();\n    return data;\n  } catch (error) {\n    console.error('Error:', error);\n  }\n};";
return [
    16 => [
        'lessons' => [
            'Основы TypeScript' => '<h2>Основы TypeScript</h2><p>TypeScript — это строго типизированный надмножество JavaScript, разработанное Microsoft. Он добавляет статическую типизацию, интерфейсы, дженерики и другие возможности для поиска ошибок на этапе компиляции.</p><h3>Установка</h3><pre><code>npm install -g typescript\\ntsc --init\\nnpx tsc file.ts</code></pre><h3>Базовые типы</h3><pre><code>let name: string = \\"Alice\\";\\nlet age: number = 25;\\nlet active: boolean = true;\\nlet data: any = \\"любой тип\\";\\nlet safe: unknown = \\"безопасный any\\";</code></pre><h3>Массивы и кортежи</h3><pre><code>let nums: number[] = [1, 2, 3];\\nlet names: Array&lt;string&gt; = [\\"Alice\\"];\\nlet tuple: [string, number] = [\\"age\\", 25];</code></pre><h3>Функции</h3><pre><code>function add(a: number, b: number): number {\\n  return a + b;\\n}\\nconst greet = (name: string = \\"Guest\\"): string =&gt; {\\n  return `Hello, ${name}!`;\\n};</code></pre><p>TypeScript автоматически выводит типы (type inference), но явное указание делает код безопаснее.</p>',
            'Интерфейсы и типы' => '<h2>Интерфейсы и типы</h2><p>TypeScript предлагает интерфейсы и псевдонимы типов для описания структуры объектов.</p><h3>Интерфейсы</h3><pre><code>interface Animal {\\n  name: string;\\n  sound(): string;\\n}\\ninterface Dog extends Animal {\\n  breed: string;\\n  bark(): string;\\n}</code></pre><h3>Псевдонимы типов</h3><pre><code>type Point = { x: number; y: number; };\\ntype ID = string | number;\\ntype Status = \\"active\\" | \\"inactive\\" | \\"pending\\";</code></pre><h3>Объединения и пересечения</h3><pre><code>let value: string | number = \\"hello\\";\\ntype Named = { name: string };\\ntype Aged = { age: number };\\ntype Person = Named &amp; Aged;</code></pre><h3>Защита типов</h3><pre><code>function format(value: string | number): string {\\n  if (typeof value === \\"string\\") return value.toUpperCase();\\n  return value.toFixed(2);\\n}</code></pre>',
            'Дженерики' => '<h2>Дженерики (Generics)</h2><p>Дженерики позволяют создавать переиспользуемый код с разными типами с сохранением типобезопасности.</p><h3>Дженерик-функции</h3><pre><code>function identity&lt;T&gt;(arg: T): T {\\n  return arg;\\n}\\nconst num = identity&lt;number&gt;(42);\\nconst str = identity(\\"hello\\");</code></pre><h3>Ограничения</h3><pre><code>function getLength&lt;T extends { length: number }&gt;(item: T): number {\\n  return item.length;\\n}</code></pre><h3>Utility Types</h3><pre><code>interface User { name: string; age: number; email: string; }\\ntype PartialUser = Partial&lt;User&gt;;\\ntype UserName = Pick&lt;User, \\"name\\" | \\"email\\"&gt;;\\ntype UserWithoutEmail = Omit&lt;User, \\"email\\"&gt;;\\ntype UserMap = Record&lt;string, User&gt;;</code></pre>',
            'Декораторы и утилиты' => '<h2>Декораторы и утилиты</h2><p>Декораторы —声明ный синтаксис для изменения классов, методов и свойств.</p><h3>Декораторы методов</h3><pre><code>function Log(target: any, key: string, descriptor: PropertyDescriptor) {\\n  const original = descriptor.value;\\n  descriptor.value = function(...args: any[]) {\\n    console.log(\\`Calling \\${key} with\\`, args);\\n    return original.apply(this, args);\\n  };\\n}\\nclass Calculator {\\n  @Log\\n  add(a: number, b: number) { return a + b; }\\n}</code></pre><h3>Перечисления</h3><pre><code>enum Direction { Up, Down, Left, Right }\\nenum Status { Active = \\"ACTIVE\\", Inactive = \\"INACTIVE\\" }\\nconst enum Color { Red = \\"RED\\", Green = \\"GREEN\\" }</code></pre><h3>Продвинутые Utility Types</h3><pre><code>type T1 = Exclude&lt;\\"a\\" | \\"b\\" | \\"c\\", \\"a\\"&gt;;\\ntype T2 = ReturnType&lt;() =&gt; string&gt;;\\ntype T3 = Parameters&lt;(a: number, b: string) =&gt; void&gt;;</code></pre>',
            'Тест по TypeScript' => '<h2>Тест по TypeScript</h2><p>Проверьте свои знания по основам TypeScript, интерфейсам, дженерикам, декораторам и утилитарным типам.</p>',
        ],
        'quizzes' => [
            'Основы TypeScript' => [
                ['q' => 'Какой тип данных используется для логических значений true/false?', 'o' => ['string', 'number', 'boolean', 'any'], 'c' => 2, 'e' => 'Тип boolean принимает значения true или false.'],
                ['q' => 'Что делает оператор ? в интерфейсе (email?: string)?', 'o' => ['Делает поле обязательным', 'Делает поле необязательным', 'Удаляет поле', 'Делает приватным'], 'c' => 1, 'e' => 'Оператор ? делает поле необязательным.'],
                ['q' => 'Какой тип является безопасной альтернативой any?', 'o' => ['void', 'never', 'unknown', 'object'], 'c' => 2, 'e' => 'unknown безопасен: требует проверки типа перед использованием.'],
                ['q' => 'Как объявить массив чисел в TypeScript?', 'o' => ['let arr: number[]', 'let arr: Array<number>', 'Оба варианта верны', 'let arr: [number]'], 'c' => 2, 'e' => 'Оба синтаксиса корректны: number[] и Array<number>.'],
                ['q' => 'Что такое type inference?', 'o' => ['Ручное указание типов', 'Автоматическое выведение типов компилятором', 'Проверка типов во время выполнения', 'Преобразование типов'], 'c' => 1, 'e' => 'Type inference — способность TypeScript автоматически определять тип переменной.'],
            ],
            'Интерфейсы и типы' => [
                ['q' => 'Чем интерфейс отличается от type alias?', 'o' => ['Ничем', 'Интерфейс расширяется через extends, type через &', 'Type быстрее', 'Интерфейс поддерживает примитивы'], 'c' => 1, 'e' => 'Интерфейсы поддерживают наследование (extends). Type поддерживает пересечение (&).'],
                ['q' => 'Что такое Union type?', 'o' => ['Тип, объединяющий два класса', 'Тип, который может быть одним из нескольких типов', 'Тип массива', 'Тип функции'], 'c' => 1, 'e' => 'Union type обозначается символом |.'],
                ['q' => 'Как проверить тип переменной (Type Guard)?', 'o' => ['typeof', 'instanceof', '"in" для проверки свойства', 'Все перечисленные'], 'c' => 3, 'e' => 'typeof для примитивов, instanceof для классов, "in" для свойств.'],
                ['q' => 'Что такое Intersection type?', 'o' => ['Пересечение двух массивов', 'Тип, объединяющий все свойства из нескольких типов', 'Математическое пересечение', 'Работа с данными'], 'c' => 1, 'e' => 'Intersection (T & U) создаёт тип со всеми свойствами из обоих типов.'],
                ['q' => 'Какое ключевое слово создаёт псевдоним типа?', 'o' => ['interface', 'type', 'class', 'alias'], 'c' => 1, 'e' => 'Ключевое слово type создаёт псевдоним для любого типа.'],
            ],
            'Дженерики' => [
                ['q' => 'Что такое дженерик в TypeScript?', 'o' => ['Тип для интернета', 'Параметризованный тип для переиспользуемого кода', 'Специальный тип', 'Модификатор доступа'], 'c' => 1, 'e' => 'Дженерики — параметры типов для создания компонентов с разными типами.'],
                ['q' => 'Как ограничить дженерик?', 'o' => ['extends', 'keyof', 'implements', 'typeof'], 'c' => 0, 'e' => 'extends задаёт ограничение: T extends SomeType.'],
                ['q' => 'Что делает Partial<T>?', 'o' => ['Делает все поля обязательными', 'Делает все поля необязательными', 'Удаляет все поля', 'Копирует все поля'], 'c' => 1, 'e' => 'Partial<T> создаёт тип, где все свойства необязательны.'],
                ['q' => 'Как получить тип возвращаемого значения функции?', 'o' => ['typeof', 'keyof', 'ReturnType', 'InstanceType'], 'c' => 2, 'e' => 'ReturnType<T> извлекает тип возвращаемого значения.'],
                ['q' => 'Что такое keyof?', 'o' => ['Оператор для получения ключей объекта как union тип', 'Оператор для удаления ключей', 'Метод перебора ключей', 'Тип для клавиатуры'], 'c' => 0, 'e' => 'keyof T возвращает union-тип всех ключей типа T.'],
            ],
            'Декораторы и утилиты' => [
                ['q' => 'Как включить декораторы?', 'o' => ['Флаг --decorators', 'experimentalDecorators: true в tsconfig.json', 'Импорт из @angular/core', 'Работают по умолчанию'], 'c' => 1, 'e' => 'Нужно включить experimentalDecorators в tsconfig.json.'],
                ['q' => 'Что такое декоратор метода?', 'o' => ['Метод для декорирования класса', 'Функция, модифицирующая поведение метода', 'Специальный тип метода', 'Метод для украшения'], 'c' => 1, 'e' => 'Декоратор метода — функция, модифицирующая поведение метода.'],
                ['q' => 'Какое перечисление задаёт строковые значения?', 'o' => ['Числовое', 'Строковое', 'Константное', 'Любое'], 'c' => 1, 'e' => 'Строковое перечисление задаёт строковые значения.'],
                ['q' => 'Что делает Pick<T, K>?', 'o' => ['Удаляет свойства', 'Выбирает только указанные свойства из типа', 'Копирует весь тип', 'Добавляет свойства'], 'c' => 1, 'e' => 'Pick<T, K> создаёт тип с указанными свойствами из T.'],
                ['q' => 'Какой тип表示 функцию, которая никогда не завершается?', 'o' => ['void', 'null', 'never', 'undefined'], 'c' => 2, 'e' => 'never表示 функция никогда не завершается.'],
            ],
            'Тест по TypeScript' => [
                ['q' => 'Какой модификатор делает свойство приватным?', 'o' => ['public', 'private', 'protected', 'readonly'], 'c' => 1, 'e' => 'private доступен только внутри класса.'],
                ['q' => 'Что такое abstract класс?', 'o' => ['Нельзя импортировать', 'Нельзя инстанцировать, но можно наследовать', 'Класс без методов', 'Класс для абстрактных типов'], 'c' => 1, 'e' => 'Abstract класс не создаётся напрямую, но наследуется.'],
                ['q' => 'Какой тип для функции без возврата?', 'o' => ['void', 'never', 'null', 'undefined'], 'c' => 0, 'e' => 'void для функций без возврата.'],
                ['q' => 'Как безопасно использовать unknown?', 'o' => ['Привести через as', 'Проверить typeof перед использованием', 'Использовать any', 'Присвоить напрямую'], 'c' => 1, 'e' => 'Нужно проверить тип перед использованием unknown.'],
                ['q' => 'Какое ключевое слово создаёт псевдоним типа?', 'o' => ['interface', 'class', 'type', 'enum'], 'c' => 2, 'e' => 'type создаёт псевдоним для любого типа.'],
            ],
        ],
        'practice' => [
            'Основы TypeScript' => [
                ['lang' => 'typescript', 'title' => 'Типизация интерфейса', 'prompt' => 'Создайте интерфейс User с полями name, age, email (необязательное) и функцию findUser.', 'out' => '', 'start' => $startTs1, 'tests' => [['contains', 'interface'], ['contains', 'name: string'], ['contains', 'function findUser']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'typescript', 'title' => 'Union type функция', 'prompt' => 'Создайте функцию process, принимающую string | number и возвращающую строку.', 'out' => '', 'start' => $startTs2, 'tests' => [['contains', 'string | number'], ['contains', 'typeof'], ['contains', 'function process']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'typescript', 'title' => 'Кортежи', 'prompt' => 'Создайте функцию, принимающую кортеж [string, number] и возвращающую форматированную строку.', 'out' => '', 'start' => '', 'tests' => [['contains', 'function'], ['contains', '[string, number]']], 'diff' => 'medium', 'time' => 20],
            ],
            'Интерфейсы и типы' => [
                ['lang' => 'typescript', 'title' => 'Наследование интерфейсов', 'prompt' => 'Создайте интерфейс Animal и расширьте его Dog с полем breed и методом bark.', 'out' => '', 'start' => $startTs2, 'tests' => [['contains', 'interface Animal'], ['contains', 'extends'], ['contains', 'bark']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'typescript', 'title' => 'Type Guards', 'prompt' => 'Напишите функцию format с type guard для string | number.', 'out' => '', 'start' => $startTs2, 'tests' => [['contains', 'typeof'], ['contains', 'string | number'], ['contains', 'function format']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'typescript', 'title' => 'Intersection type', 'prompt' => 'Создайте типы Named и Aged и объедините их в Person через intersection.', 'out' => '', 'start' => '', 'tests' => [['contains', 'type Named'], ['contains', 'type Aged'], ['contains', '&']], 'diff' => 'easy', 'time' => 15],
            ],
            'Дженерики' => [
                ['lang' => 'typescript', 'title' => 'Дженерик-функция', 'prompt' => 'Напишите дженерик-функцию merge для объединения двух объектов.', 'out' => '', 'start' => $startTs3, 'tests' => [['contains', '<T'], ['contains', 'extends object'], ['contains', 'function merge']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'typescript', 'title' => 'Utility Types', 'prompt' => 'Используйте Partial, Pick и Omit с интерфейсом User.', 'out' => '', 'start' => $startTs4, 'tests' => [['contains', 'Partial'], ['contains', 'Pick'], ['contains', 'Omit']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'typescript', 'title' => 'Дженерик-интерфейс', 'prompt' => 'Создайте дженерик-интерфейс ApiResponse<T> с полями data, status, message.', 'out' => '', 'start' => '', 'tests' => [['contains', 'interface ApiResponse'], ['contains', '<T>'], ['contains', 'data: T']], 'diff' => 'hard', 'time' => 25],
            ],
            'Декораторы и утилиты' => [
                ['lang' => 'typescript', 'title' => 'Декоратор метода', 'prompt' => 'Создайте декоратор Log для логирования имени метода и аргументов.', 'out' => '', 'start' => $startTsDecorator, 'tests' => [['contains', 'function Log'], ['contains', 'descriptor.value'], ['contains', 'console.log']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'typescript', 'title' => 'Строковое перечисление', 'prompt' => 'Создайте строковое перечисление UserRole и функцию проверки роли.', 'out' => '', 'start' => $startTs4, 'tests' => [['contains', 'enum UserRole'], ['contains', '"admin"'], ['contains', 'function']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'typescript', 'title' => 'Фабрика декораторов', 'prompt' => 'Создайте фабрику Throttle для ограничения частоты вызова метода.', 'out' => '', 'start' => '', 'tests' => [['contains', 'function Throttle'], ['contains', 'return function'], ['contains', 'Date.now']], 'diff' => 'hard', 'time' => 25],
            ],
            'Тест по TypeScript' => [
                ['lang' => 'typescript', 'title' => 'Абстрактный класс', 'prompt' => 'Создайте абстрактный класс Shape с методом area() и класс Circle.', 'out' => '', 'start' => '', 'tests' => [['contains', 'abstract class'], ['contains', 'area'], ['contains', 'class Circle']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'typescript', 'title' => 'Типизированная функция', 'prompt' => 'Напишите функцию groupBy для группировки массива по ключу с keyof.', 'out' => '', 'start' => '', 'tests' => [['contains', 'function groupBy'], ['contains', 'keyof'], ['contains', 'Record']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'typescript', 'title' => 'Never и void', 'prompt' => 'Создайте функцию throwError (never) и logMessage (void).', 'out' => '', 'start' => '', 'tests' => [['contains', ': never'], ['contains', ': void'], ['contains', 'throw']], 'diff' => 'medium', 'time' => 20],
            ],
        ]
    ],
    17 => [
        'lessons' => [
            'Основы Docker' => '<h2>Основы Docker</h2><p>Docker — платформа для контейнеризации, упаковывающая приложение со всеми зависимостями в изолированный контейнер.</p><h3>Контейнеры vs ВМ</h3><ul><li><strong>Контейнеры</strong> — лёгкие, делят ядро ОС</li><li><strong>ВМ</strong> — тяжёлые, каждая со своей ОС</li></ul><h3>Базовые команды</h3><pre><code>docker pull nginx:latest\\ndocker run -d -p 8080:80 --name web nginx\\ndocker ps\\ndocker logs web\\ndocker stop web\\ndocker rm web\\ndocker images</code></pre><h3>Порты и переменные</h3><pre><code>docker run -d -p 3000:3000 myapp\\ndocker run -e NODE_ENV=production myapp</code></pre><h3>Управление</h3><pre><code>docker exec -it web bash\\ndocker stats\\ndocker container prune</code></pre>',
            'Dockerfile и образы' => '<h2>Dockerfile и образы</h2><p>Dockerfile — текстовый файл с инструкциями для сборки Docker-образа.</p><h3>Основные инструкции</h3><pre><code>FROM node:18-alpine\\nWORKDIR /app\\nCOPY package*.json ./\\nRUN npm ci --production\\nCOPY . .\\nEXPOSE 3000\\nCMD ["node", "server.js"]</code></pre><h3>.dockerignore</h3><pre><code>node_modules\\nnpm-debug.log\\n.git\\n.env</code></pre><h3>Multi-stage сборка</h3><pre><code>FROM node:18-alpine AS builder\\nWORKDIR /app\\nCOPY . .\\nRUN npm ci && npm run build\\n\\nFROM node:18-alpine\\nWORKDIR /app\\nCOPY --from=builder /app/dist ./dist\\nEXPOSE 3000\\nCMD ["node", "dist/server.js"]</code></pre>',
            'Docker Compose' => '<h2>Docker Compose</h2><p>Docker Compose — инструмент для многоконтейнерных приложений в одном YAML-файле.</p><h3>docker-compose.yml</h3><pre><code>version: "3.8"\\nservices:\\n  web:\\n    build: .\\n    ports:\\n      - "3000:3000"\\n    depends_on:\\n      - db\\n  db:\\n    image: postgres:15\\n    environment:\\n      POSTGRES_PASSWORD: secret\\n    volumes:\\n      - db-data:/var/lib/postgresql/data\\nvolumes:\\n  db-data:</code></pre><h3>Команды</h3><pre><code>docker compose up -d\\ndocker compose down\\ndocker compose logs -f web\\ndocker compose build --no-cache\\ndocker compose exec web bash</code></pre>',
            'Сети и тома' => '<h2>Сети и тома</h2><p>Сети и тома — ключевые концепции Docker для связи контейнеров и хранения данных.</p><h3>Типы сетей</h3><ul><li><strong>bridge</strong> — по умолчанию</li><li><strong>host</strong> — сеть хоста</li><li><strong>overlay</strong> — для Swarm</li><li><strong>none</strong> — без сети</li></ul><h3>Управление сетями</h3><pre><code>docker network create mynet\\ndocker run -d --network mynet --name web nginx\\ndocker network ls</code></pre><h3>Тома (Volumes)</h3><pre><code>docker volume create mydata\\ndocker run -d -v mydata:/var/lib/postgresql/data postgres\\ndocker run -d -v $(pwd)/src:/app/src myapp\\ndocker volume ls</code></pre>',
            'Тест по Docker' => '<h2>Тест по Docker</h2><p>Проверьте свои знания по основам Docker, Dockerfile, Docker Compose, сетям и томам.</p>',
        ],
        'quizzes' => [
            'Основы Docker' => [
                ['q' => 'Что такое Docker-контейнер?', 'o' => ['Виртуальная машина', 'Изолированный процесс с файловой системой', 'Файл конфигурации', 'База данных'], 'c' => 1, 'e' => 'Контейнер — изолированный процесс с файловой системой.'],
                ['q' => 'Какой флаг маппит порт 8080 хоста на порт 80?', 'o' => ['--port 8080:80', '-p 8080:80', '--expose 8080:80', '-P 8080:80'], 'c' => 1, 'e' => 'Флаг -p маппит порт: хост:контейнер.'],
                ['q' => 'Как посмотреть логи контейнера?', 'o' => ['docker show logs web', 'docker logs web', 'docker inspect web', 'docker cat web'], 'c' => 1, 'e' => 'docker logs показывает stdout/stderr контейнера.'],
                ['q' => 'Как запустить контейнер в фоне?', 'o' => ['docker run --bg', 'docker run -d', 'docker run --detach', 'Оба варианта -d и --detach'], 'c' => 3, 'e' => 'Флаг -d (--detach) запускает в фоновом режиме.'],
                ['q' => 'Что такое Docker image?', 'o' => ['Запущенный контейнер', 'Шаблон для создания контейнера', 'Файл конфигурации', 'Лог ошибок'], 'c' => 1, 'e' => 'Образ — неизменяемый шаблон для контейнера.'],
            ],
            'Dockerfile и образы' => [
                ['q' => 'Какая инструкция задаёт базовый образ?', 'o' => ['BASE', 'FROM', 'IMAGE', 'INIT'], 'c' => 1, 'e' => 'FROM — первая инструкция, задающая базовый образ.'],
                ['q' => 'Чем COPY отличается от ADD?', 'o' => ['Ничем', 'ADD загружает URL и распаковывает tar', 'COPY быстрее', 'ADD только для локальных'], 'c' => 1, 'e' => 'ADD может загружать по URL и распаковывать tar.'],
                ['q' => 'Какая инструкция задаёт команду запуска?', 'o' => ['RUN', 'CMD', 'ENTRYPOINT', 'START'], 'c' => 1, 'e' => 'CMD задаёт команду по умолчанию.'],
                ['q' => 'Для чего EXPOSE?', 'o' => ['Открывает порт', 'Документирует порт контейнера', 'Запрещает доступ', 'Создаёт файрвол'], 'c' => 1, 'e' => 'EXPOSE — документация. Реальное открытие через -p.'],
                ['q' => 'Как уменьшить размер образа?', 'o' => ['Multi-stage build', 'Alpine-образы', '.dockerignore', 'Все перечисленные'], 'c' => 3, 'e' => 'Все три способа уменьшают размер образа.'],
            ],
            'Docker Compose' => [
                ['q' => 'Как запустить все сервисы?', 'o' => ['docker compose start', 'docker compose up -d', 'docker compose run', 'docker compose launch'], 'c' => 1, 'e' => 'docker compose up -d запускает все сервисы в фоне.'],
                ['q' => 'Как указать зависимость?', 'o' => ['depends_on: db', 'requires: db', 'needs: db', 'links: db'], 'c' => 0, 'e' => 'depends_on гарантирует запуск db перед web.'],
                ['q' => 'Где Compose ищет переменные по умолчанию?', 'o' => ['.env файл', 'Переменные ОС', 'docker-compose.yml', 'Dockerfile'], 'c' => 0, 'e' => 'Compose читает переменные из .env файла.'],
                ['q' => 'Как остановить и удалить всё?', 'o' => ['docker compose stop', 'docker compose down', 'docker compose destroy', 'docker compose clean'], 'c' => 1, 'e' => 'docker compose down останавливает и удаляет контейнеры.'],
                ['q' => 'Как пересобрать без кэша?', 'o' => ['docker compose build', 'docker compose build --no-cache', 'docker compose rebuild', 'docker compose --fresh build'], 'c' => 1, 'e' => '--no-cache отключает кэширование.'],
            ],
            'Сети и тома' => [
                ['q' => 'Какая сеть по умолчанию?', 'o' => ['host', 'bridge', 'overlay', 'none'], 'c' => 1, 'e' => 'bridge — сеть по умолчанию.'],
                ['q' => 'Что такое bind mount?', 'o' => ['Именованный том', 'Маппинг директории хоста в контейнер', 'Тип сети', 'Шифрование данных'], 'c' => 1, 'e' => 'Bind mount монтирует директорию хоста.'],
                ['q' => 'Как создать именованный том?', 'o' => ['docker volume new mydata', 'docker volume create mydata', 'docker create volume mydata', 'docker volume init mydata'], 'c' => 1, 'e' => 'docker volume create создаёт том.'],
                ['q' => 'Как сделать том только для чтения?', 'o' => ['-v mydata:/data:rw', '-v mydata:/data:ro', '-v mydata:/data:readonly', '--read-only -v mydata:/data'], 'c' => 1, 'e' => ':ro делает том read-only.'],
                ['q' => 'Что с данными без тома при удалении контейнера?', 'o' => ['Сохранятся', 'Удалятся безвозвратно', 'Переместятся', 'Заархивируются'], 'c' => 1, 'e' => 'Без тома данные временные и удаляются.'],
            ],
            'Тест по Docker' => [
                ['q' => 'Какой размер минимального Alpine-образа?', 'o' => ['~500 MB', '~5 MB', '~50 MB', '~1 GB'], 'c' => 1, 'e' => 'Alpine — ~5-20 MB.'],
                ['q' => 'Как посмотреть слои образа?', 'o' => ['docker image layers nginx', 'docker history nginx', 'docker inspect --layers nginx', 'docker show nginx'], 'c' => 1, 'e' => 'docker history показывает слои.'],
                ['q' => 'Как удалить все неиспользуемые ресурсы?', 'o' => ['docker system prune', 'docker clean --all', 'docker purge', 'docker remove --all'], 'c' => 0, 'e' => 'docker system prune удаляет всё неиспользуемое.'],
                ['q' => 'Какой формат CMD предпочтительнее?', 'o' => ['CMD node server.js', 'CMD ["node", "server.js"]', 'Без разницы', 'CMD RUN node server.js'], 'c' => 1, 'e' => 'Exec form корректно обрабатывает сигналы.'],
                ['q' => 'Как ограничить ресурсы контейнера?', 'o' => ['--cpus=2 --memory=1g', '--limit-cpu=2 --limit-memory=1g', '--cpu=2 --ram=1g', 'docker resource limit cpu=2'], 'c' => 0, 'e' => '--cpus и --memory ограничивают ресурсы.'],
            ],
        ],
        'practice' => [
            'Основы Docker' => [
                ['lang' => 'bash', 'title' => 'Базовые команды', 'prompt' => 'Скачайте образ nginx, запустите контейнер с маппингом портов и посмотрите логи.', 'out' => '', 'start' => $startDockerCmds, 'tests' => [['contains', 'docker pull'], ['contains', 'docker run'], ['contains', 'docker logs']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'bash', 'title' => 'Управление контейнерами', 'prompt' => 'Просмотрите запущенные контейнеры, подключитесь и остановите.', 'out' => '', 'start' => $startDockerCmds, 'tests' => [['contains', 'docker ps'], ['contains', 'docker exec'], ['contains', 'docker stop']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'bash', 'title' => 'Переменные окружения', 'prompt' => 'Запустите контейнер с переменными NODE_ENV и DB_HOST.', 'out' => '', 'start' => '', 'tests' => [['contains', '-e NODE_ENV'], ['contains', 'docker exec']], 'diff' => 'medium', 'time' => 15],
            ],
            'Dockerfile и образы' => [
                ['lang' => 'dockerfile', 'title' => 'Dockerfile для Node.js', 'prompt' => 'Создайте Dockerfile для Node.js приложения.', 'out' => '', 'start' => $startDockerfile16, 'tests' => [['contains', 'FROM'], ['contains', 'WORKDIR'], ['contains', 'COPY'], ['contains', 'RUN']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'dockerfile', 'title' => 'Multi-stage build', 'prompt' => 'Создайте multi-stage Dockerfile для TypeScript.', 'out' => '', 'start' => $startDockerfile16, 'tests' => [['contains', 'FROM'], ['contains', 'AS builder'], ['contains', 'COPY --from=builder']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'dockerfile', 'title' => '.dockerignore', 'prompt' => 'Создайте .dockerignore для исключения node_modules и .env.', 'out' => '', 'start' => '', 'tests' => [['contains', 'node_modules'], ['contains', '.git'], ['contains', '.env']], 'diff' => 'easy', 'time' => 10],
            ],
            'Docker Compose' => [
                ['lang' => 'yaml', 'title' => 'Multi-service compose', 'prompt' => 'Создайте docker-compose.yml для Node.js с PostgreSQL.', 'out' => '', 'start' => $startCompose16, 'tests' => [['contains', 'services'], ['contains', 'postgres']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'yaml', 'title' => 'Compose с сетями', 'prompt' => 'Настройте изолированную сеть для frontend и backend.', 'out' => '', 'start' => $startCompose16, 'tests' => [['contains', 'networks:'], ['contains', 'frontend'], ['contains', 'backend']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'yaml', 'title' => 'Compose с томами', 'prompt' => 'Добавьте именованный том для PostgreSQL.', 'out' => '', 'start' => $startCompose16, 'tests' => [['contains', 'volumes:'], ['contains', 'db-data']], 'diff' => 'medium', 'time' => 20],
            ],
            'Сети и тома' => [
                ['lang' => 'bash', 'title' => 'Создание сети', 'prompt' => 'Создайте сеть и подключите два контейнера.', 'out' => '', 'start' => '', 'tests' => [['contains', 'docker network create'], ['contains', '--network']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'bash', 'title' => 'Управление томами', 'prompt' => 'Создайте именованный том и смонтируйте в контейнер.', 'out' => '', 'start' => '', 'tests' => [['contains', 'docker volume create'], ['contains', '-v']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'bash', 'title' => 'Bind mount', 'prompt' => 'Смонтируйте локальную директорию src в контейнер.', 'out' => '', 'start' => '', 'tests' => [['contains', '-v'], ['contains', '/app/src']], 'diff' => 'easy', 'time' => 10],
            ],
            'Тест по Docker' => [
                ['lang' => 'dockerfile', 'title' => 'Healthcheck', 'prompt' => 'Добавьте healthcheck в Dockerfile.', 'out' => '', 'start' => $startDockerfile16, 'tests' => [['contains', 'HEALTHCHECK'], ['contains', 'curl']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'yaml', 'title' => 'Compose env файл', 'prompt' => 'Настройте env_file в docker-compose.yml.', 'out' => '', 'start' => $startCompose16, 'tests' => [['contains', 'env_file'], ['contains', '.env']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'bash', 'title' => 'Очистка Docker', 'prompt' => 'Выполните полную очистку неиспользуемых ресурсов.', 'out' => '', 'start' => '', 'tests' => [['contains', 'docker system prune']], 'diff' => 'easy', 'time' => 10],
            ],
        ]
    ],

    18 => [
        'lessons' => [
            'Архитектура Kubernetes' => '<h2>Архитектура Kubernetes</h2><p>Kubernetes — система оркестрации контейнеров для автоматического развёртывания и управления приложениями.</p><h3>Control Plane</h3><ul><li><strong>API Server</strong> — точка входа</li><li><strong>Scheduler</strong> — назначает Pod на ноды</li><li><strong>Controller Manager</strong> — управляет контроллерами</li><li><strong>etcd</strong> — хранилище состояния кластера</li></ul><h3>Worker Nodes</h3><ul><li><strong>kubelet</strong> — агент на ноде</li><li><strong>kube-proxy</strong> — сетевые правила</li><li><strong>Container Runtime</strong> — Docker, containerd</li></ul><h3>kubectl</h3><pre><code>kubectl cluster-info\\nkubectl get nodes\\nkubectl get pods -A\\nkubectl describe pod my-pod\\nkubectl logs my-pod\\nkubectl exec -it my-pod -- bash</code></pre>',
            'Поды и контроллеры' => '<h2>Поды и контроллеры</h2><p>Pod — минимальная единица развертывания. Контроллеры управляют жизненным циклом Pod.</p><h3>Pod</h3><pre><code>apiVersion: v1\\nkind: Pod\\nmetadata:\\n  name: my-pod\\nspec:\\n  containers:\\n  - name: app\\n    image: nginx:latest\\n    ports:\\n    - containerPort: 80</code></pre><h3>Deployment</h3><pre><code>apiVersion: apps/v1\\nkind: Deployment\\nmetadata:\\n  name: my-deployment\\nspec:\\n  replicas: 3\\n  strategy:\\n    type: RollingUpdate\\n    rollingUpdate:\\n      maxSurge: 1\\n      maxUnavailable: 0\\n  selector:\\n    matchLabels:\\n      app: my-app\\n  template:\\n    metadata:\\n      labels:\\n        app: my-app\\n    spec:\\n      containers:\\n      - name: app\\n        image: nginx:1.25</code></pre><h3>Команды</h3><pre><code>kubectl apply -f deployment.yaml\\nkubectl rollout status deployment/my-deployment\\nkubectl rollout undo deployment/my-deployment\\nkubectl scale deployment my-deployment --replicas=5</code></pre>',
            'Сервисы и ингресс' => '<h2>Сервисы и ингресс</h2><p>Service обеспечивает стабильную точку доступа к Pod. Ingress управляет HTTP/HTTPS маршрутизацией.</p><h3>Типы Service</h3><ul><li><strong>ClusterIP</strong> — только внутри кластера</li><li><strong>NodePort</strong> — через порт ноды</li><li><strong>LoadBalancer</strong> — внешний балансировщик</li></ul><h3>Service</h3><pre><code>apiVersion: v1\\nkind: Service\\nmetadata:\\n  name: my-service\\nspec:\\n  selector:\\n    app: my-app\\n  ports:\\n  - port: 80\\n    targetPort: 8080\\n  type: LoadBalancer</code></pre><h3>Ingress</h3><pre><code>apiVersion: networking.k8s.io/v1\\nkind: Ingress\\nmetadata:\\n  name: my-ingress\\nspec:\\n  rules:\\n  - host: myapp.example.com\\n    http:\\n      paths:\\n      - path: /api\\n        pathType: Prefix\\n        backend:\\n          service:\\n            name: api-service\\n            port:\\n              number: 80</code></pre>',
            'Конфигурация и секреты' => '<h2>Конфигурация и секреты</h2><p>ConfigMap и Secret позволяют управлять конфигурацией отдельно от образов.</p><h3>ConfigMap</h3><pre><code>apiVersion: v1\\nkind: ConfigMap\\nmetadata:\\n  name: app-config\\ndata:\\n  DATABASE_URL: "postgres://db:5432/mydb"\\n  LOG_LEVEL: "info"</code></pre><h3>Secret</h3><pre><code>apiVersion: v1\\nkind: Secret\\nmetadata:\\n  name: app-secrets\\ntype: Opaque\\ndata:\\n  DB_PASSWORD: cGFzc3dvcmQxMjM=\\n  API_KEY: YWJjZGVmZzEyMw==</code></pre><h3>Использование</h3><pre><code>spec:\\n  containers:\\n  - name: app\\n    envFrom:\\n    - configMapRef:\\n        name: app-config\\n    - secretRef:\\n        name: app-secrets</code></pre><h3>Команды</h3><pre><code>kubectl create configmap my-config --from-literal=key1=value1\\nkubectl create secret generic my-secret --from-literal=password=abc123\\nkubectl get configmaps\\nkubectl get secrets</code></pre>',
            'Тест по Kubernetes' => '<h2>Тест по Kubernetes</h2><p>Проверьте знания по архитектуре K8s, Pod, Deployment, Services и ConfigMap/Secret.</p>',
        ],
        'quizzes' => [
            'Архитектура Kubernetes' => [
                ['q' => 'Что такое Kubernetes?', 'o' => ['Контейнер', 'Система оркестрации контейнеров', 'База данных', 'Язык программирования'], 'c' => 1, 'e' => 'Kubernetes — система оркестрации контейнеров.'],
                ['q' => 'Какой компонент хранит состояние кластера?', 'o' => ['API Server', 'Scheduler', 'etcd', 'kubelet'], 'c' => 2, 'e' => 'etcd — распределённое хранилище состояния.'],
                ['q' => 'Что делает kubelet?', 'o' => ['Управляет сетью', 'Агент на ноде, управляет Pod', 'Маршрутизирует трафик', 'Хранит образы'], 'c' => 1, 'e' => 'kubelet — агент на каждой ноде.'],
                ['q' => 'Как показать все поды?', 'o' => ['kubectl get pods', 'kubectl get pods -A', 'kubectl list pods', 'kubectl show pods'], 'c' => 1, 'e' => '-A показывает Pod во всех namespace.'],
                ['q' => 'Что такое Pod?', 'o' => ['Контейнер', 'Минимальная единица развертывания', 'Сеть', 'Нода'], 'c' => 1, 'e' => 'Pod — минимальная единица в K8s.'],
            ],
            'Поды и контроллеры' => [
                ['q' => 'Какой контроллер управляет репликами?', 'o' => ['Deployment', 'ReplicaSet', 'Service', 'ConfigMap'], 'c' => 1, 'e' => 'ReplicaSet поддерживает количество реплик.'],
                ['q' => 'Что такое RollingUpdate?', 'o' => ['Полная остановка', 'Постепенное обновление без downtime', 'Откат', 'Удаление всех Pod'], 'c' => 1, 'e' => 'RollingUpdate постепенно заменяет Pod.'],
                ['q' => 'Как откатить rollout?', 'o' => ['kubectl rollback', 'kubectl rollout undo', 'kubectl undo', 'kubectl revert'], 'c' => 1, 'e' => 'kubectl rollout undo откатывает последний rollout.'],
                ['q' => 'Как масштабировать Deployment?', 'o' => ['kubectl scale deployment my-deploy --replicas=5', 'kubectl resize my-deploy 5', 'kubectl set replicas my-deploy 5', 'kubectl scale --count 5'], 'c' => 0, 'e' => 'kubectl scale с --replicas задаёт количество.'],
                ['q' => 'Что делает maxSurge: 1?', 'o' => ['Максимум 1 Pod недоступен', 'Максимум 1 дополнительный Pod', 'Минимум 1 Pod', 'Максимум 1 обновление'], 'c' => 1, 'e' => 'maxSurge: 1 — максимум 1 доп. Pod сверх desired.'],
            ],
            'Сервисы и ингресс' => [
                ['q' => 'Какой тип Service по умолчанию?', 'o' => ['NodePort', 'LoadBalancer', 'ClusterIP', 'ExternalName'], 'c' => 2, 'e' => 'ClusterIP — по умолчанию, только внутри кластера.'],
                ['q' => 'Какой тип открывает порт на нодах?', 'o' => ['ClusterIP', 'NodePort', 'LoadBalancer', 'Headless'], 'c' => 1, 'e' => 'NodePort открывает порт 30000-32767.'],
                ['q' => 'Для чего Ingress?', 'o' => ['Управление Pod', 'Маршрутизация HTTP/HTTPS', 'Хранение данных', 'Мониторинг'], 'c' => 1, 'e' => 'Ingress маршрутизирует HTTP/HTTPS трафик.'],
                ['q' => 'Как посмотреть Endpoints?', 'o' => ['kubectl get endpoints my-service', 'kubectl show ep', 'kubectl endpoints list', 'kubectl describe ep'], 'c' => 0, 'e' => 'kubectl get endpoints показывает IP Pod.'],
                ['q' => 'Что такое LoadBalancer?', 'o' => ['Внутренний балансировщик', 'Внешний балансировщик (AWS, GCP)', 'Балансировщик Pod', 'Балансировщик сетей'], 'c' => 1, 'e' => 'LoadBalancer создаёт внешний балансировщик.'],
            ],
            'Конфигурация и секреты' => [
                ['q' => 'Чем ConfigMap отличается от Secret?', 'o' => ['Ничем', 'Secret шифрует данные', 'ConfigMap шифрует', 'Secret только для паролей'], 'c' => 1, 'e' => 'Secret хранит в base64 с опцией шифрования.'],
                ['q' => 'Как создать ConfigMap из литералов?', 'o' => ['kubectl create configmap my-config --from-literal=key1=val1', 'kubectl configmap create key1=val1', 'kubectl create cm key1=val1', 'kubectl new configmap key1=val1'], 'c' => 0, 'e' => 'kubectl create configmap с --from-literal.'],
                ['q' => 'Как использовать ConfigMap как env?', 'o' => ['envFrom с configMapRef', 'volumes с configMap', 'env с valueFrom', 'configMap с env'], 'c' => 0, 'e' => 'envFrom с configMapRef импортирует все ключи.'],
                ['q' => 'Как закодировать в base64?', 'o' => ['echo "text" | base64', 'base64 encode "text"', 'echo "text" > base64', 'base64 --encode'], 'c' => 0, 'e' => 'echo "text" | base64 кодирует строку.'],
                ['q' => 'Как посмотреть Secret?', 'o' => ['kubectl get secrets', 'kubectl describe secret my-secret', 'kubectl show secret', 'kubectl secret list'], 'c' => 1, 'e' => 'kubectl describe secret показывает метаданные.'],
            ],
            'Тест по Kubernetes' => [
                ['q' => 'Какой файл описывает ресурсы K8s?', 'o' => ['Dockerfile', 'docker-compose.yml', 'YAML манифест', '.env'], 'c' => 2, 'e' => 'Kubernetes использует YAML-манифесты.'],
                ['q' => 'Как применить манифест?', 'o' => ['kubectl create -f', 'kubectl apply -f', 'kubectl deploy', 'kubectl install'], 'c' => 1, 'e' => 'kubectl apply -f создаёт или обновляет ресурс.'],
                ['q' => 'Как удалить ресурс?', 'o' => ['kubectl delete -f', 'kubectl remove -f', 'kubectl destroy -f', 'kubectl drop -f'], 'c' => 0, 'e' => 'kubectl delete -f удаляет ресурс.'],
                ['q' => 'Как посмотреть все ресурсы в namespace?', 'o' => ['kubectl get all', 'kubectl list all', 'kubectl show all', 'kubectl describe all'], 'c' => 0, 'e' => 'kubectl get all показывает все ресурсы.'],
                ['q' => 'Что такое namespace?', 'o' => ['Физическое разделение', 'Виртуальное разделение кластера', 'Тип ноды', 'Сетевой домен'], 'c' => 1, 'e' => 'Namespace — виртуальное разделение кластера.'],
            ],
        ],
        'practice' => [
            'Архитектура Kubernetes' => [
                ['lang' => 'bash', 'title' => 'Просмотр кластера', 'prompt' => 'Проверьте информацию о кластере и список нод.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl cluster-info'], ['contains', 'kubectl get nodes']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'bash', 'title' => 'Описание ресурсов', 'prompt' => 'Получите детальную информацию о ноде и Pod.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl describe node'], ['contains', 'kubectl describe pod']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'bash', 'title' => 'Просмотр логов', 'prompt' => 'Посмотрите логи Pod и подключитесь к нему.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl logs'], ['contains', 'kubectl exec -it']], 'diff' => 'medium', 'time' => 15],
            ],
            'Поды и контроллеры' => [
                ['lang' => 'yaml', 'title' => 'Создание Deployment', 'prompt' => 'Создайте Deployment для nginx с 3 репликами.', 'out' => '', 'start' => $startK8sDeploy, 'tests' => [['contains', 'kind: Deployment'], ['contains', 'replicas: 3'], ['contains', 'image: nginx']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'yaml', 'title' => 'Rolling Update', 'prompt' => 'Настройте RollingUpdate с maxSurge: 1.', 'out' => '', 'start' => $startK8sDeploy, 'tests' => [['contains', 'RollingUpdate'], ['contains', 'maxSurge']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'bash', 'title' => 'Управление rollout', 'prompt' => 'Проверьте статус и откатите rollout.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl rollout status'], ['contains', 'kubectl rollout undo']], 'diff' => 'medium', 'time' => 15],
            ],
            'Сервисы и ингресс' => [
                ['lang' => 'yaml', 'title' => 'LoadBalancer Service', 'prompt' => 'Создайте Service типа LoadBalancer.', 'out' => '', 'start' => $startK8sService, 'tests' => [['contains', 'kind: Service'], ['contains', 'type: LoadBalancer']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'yaml', 'title' => 'Ingress маршруты', 'prompt' => 'Создайте Ingress с маршрутами /api и /.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kind: Ingress'], ['contains', '/api'], ['contains', 'pathType: Prefix']], 'diff' => 'hard', 'time' => 25],
                ['lang' => 'bash', 'title' => 'Просмотр Service', 'prompt' => 'Посмотрите Service и Endpoints.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl get svc'], ['contains', 'kubectl get endpoints']], 'diff' => 'easy', 'time' => 10],
            ],
            'Конфигурация и секреты' => [
                ['lang' => 'yaml', 'title' => 'ConfigMap', 'prompt' => 'Создайте ConfigMap с переменными окружения.', 'out' => '', 'start' => $startK8sConfig, 'tests' => [['contains', 'kind: ConfigMap'], ['contains', 'data:'], ['contains', 'DATABASE_URL']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'yaml', 'title' => 'Secret', 'prompt' => 'Создайте Secret для пароля и API ключа.', 'out' => '', 'start' => $startK8sConfig, 'tests' => [['contains', 'kind: Secret'], ['contains', 'type: Opaque']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'bash', 'title' => 'Создание секрета', 'prompt' => 'Создайте Secret через kubectl из литералов.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl create secret'], ['contains', '--from-literal']], 'diff' => 'medium', 'time' => 15],
            ],
            'Тест по Kubernetes' => [
                ['lang' => 'bash', 'title' => 'Применение манифестов', 'prompt' => 'Примените YAML для Deployment и Service.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl apply -f']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'yaml', 'title' => 'Namespace', 'prompt' => 'Создайте namespace и разверните в него приложение.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kind: Namespace'], ['contains', '-n']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'bash', 'title' => 'Мониторинг', 'prompt' => 'Проверьте использование ресурсов нод и Pod.', 'out' => '', 'start' => '', 'tests' => [['contains', 'kubectl top nodes'], ['contains', 'kubectl top pods']], 'diff' => 'medium', 'time' => 15],
            ],
        ]
    ],
    19 => [
        'lessons' => [
            'Основы мобильной разработки' => '<h2>Основы мобильной разработки</h2><p>React Native позволяет писать кроссплатформенные приложения на JavaScript для iOS и Android.</p><h3>Создание проекта</h3><pre><code>npx react-native init MyApp\\ncd MyApp\\nnpx react-native run-android\\nnpx react-native run-ios</code></pre><h3>Структура проекта</h3><pre><code>MyApp/\\n  android/\\n  ios/\\n  src/\\n    components/\\n    screens/\\n  App.js\\n  package.json</code></pre>',
            'React Native компоненты' => '<h2>React Native компоненты</h2><p>React Native предоставляет базовые компоненты для мобильного интерфейса.</p><h3>Основные компоненты</h3><pre><code>import React from \'react\';\\nimport { View, Text, Image, StyleSheet,\\n  TouchableOpacity, TextInput, FlatList } from \'react-native\';</code></pre><h3>View и Text</h3><pre><code><View style={styles.container}>\\n  <Text style={styles.title}>Заголовок</Text>\\n</View></code></pre><h3>FlatList</h3><pre><code><FlatList data={items}\\n  keyExtractor={(item) => item.id}\\n  renderItem={({ item }) => (\\n    <View><Text>{item.title}</Text></View>\\n  )} /></code></pre><h3>StyleSheet</h3><pre><code>const styles = StyleSheet.create({\\n  container: { flex: 1, padding: 16 },\\n  title: { fontSize: 24, fontWeight: \'bold\' },\\n});</code></pre>',
            'Навигация и стейт' => '<h2>Навигация и стейт</h2><p>React Navigation — стандартная библиотека для навигации.</p><h3>Stack Navigator</h3><pre><code>import { NavigationContainer } from \'@react-navigation/native\';\\nimport { createStackNavigator } from \'@react-navigation/stack\';\\nconst Stack = createStackNavigator();\\nfunction App() {\\n  return (\\n    <NavigationContainer>\\n      <Stack.Navigator>\\n        <Stack.Screen name=\'Home\' component={HomeScreen} />\\n        <Stack.Screen name=\'Details\' component={DetailsScreen} />\\n      </Stack.Navigator>\\n    </NavigationContainer>\\n  );\\n}</code></pre><h3>Параметры</h3><pre><code>navigation.navigate(\'Details\', { itemId: 42 });\\nfunction DetailsScreen({ route }) {\\n  const { itemId } = route.params;\\n}</code></pre>',
            'Работа с API' => '<h2>Работа с API</h2><p>React Native поддерживает fetch API для HTTP-запросов.</p><h3>Fetch API</h3><pre><code>const fetchUsers = async () => {\\n  try {\\n    const response = await fetch(\'https://api.example.com/users\');\\n    const data = await response.json();\\n    return data;\\n  } catch (error) {\\n    console.error(\'Error:\', error);\\n  }\\n};</code></pre><h3>Обработка ошибок</h3><pre><code>const fetchData = async () => {\\n  setLoading(true);\\n  try {\\n    const response = await fetch(url);\\n    if (!response.ok) throw new Error(response.statusText);\\n    setData(await response.json());\\n  } catch (err) {\\n    setError(err.message);\\n  } finally {\\n    setLoading(false);\\n  }\\n};</code></pre>',
            'Тест по Mobile Dev' => '<h2>Тест по Mobile Dev</h2><p>Проверьте знания по компонентам React Native, навигации и работе с API.</p>',
        ],
        'quizzes' => [
            'Основы мобильной разработки' => [
                ['q' => 'Что такое React Native?', 'o' => ['Фреймворк для веба', 'Библиотека для кроссплатформенных приложений', 'Язык программирования', 'IDE'], 'c' => 1, 'e' => 'React Native — библиотека для мобильных приложений.'],
                ['q' => 'Как создать проект?', 'o' => ['react-native create', 'npx react-native init', 'npm init react-native', 'npx create-react-native'], 'c' => 1, 'e' => 'npx react-native init.'],
                ['q' => 'Чем Expo отличается от Bare?', 'o' => ['Ничем', 'Expo managed; Bare полный контроль', 'Expo быстрее', 'Bare не поддерживает iOS'], 'c' => 1, 'e' => 'Expo managed workflow, Bare полный доступ.'],
                ['q' => 'Где JavaScript код?', 'o' => ['В android/', 'В корне и src/', 'В ios/', 'В node_modules/'], 'c' => 1, 'e' => 'Код в App.js или src/.'],
                ['q' => 'Как запустить на Android?', 'o' => ['npm start android', 'npx react-native run-android', 'react-native run', 'npm run android'], 'c' => 1, 'e' => 'npx react-native run-android.'],
            ],
            'React Native компоненты' => [
                ['q' => 'Какой компонент вместо div?', 'o' => ['Container', 'View', 'Box', 'Panel'], 'c' => 1, 'e' => 'View — базовый контейнер.'],
                ['q' => 'Какой компонент для текста?', 'o' => ['Label', 'Text', 'Span', 'P'], 'c' => 1, 'e' => 'Text — компонент для текста.'],
                ['q' => 'Какой компонент для длинных списков?', 'o' => ['ScrollView', 'FlatList', 'ListView', 'ScrollArea'], 'c' => 1, 'e' => 'FlatList оптимизирован для больших списков.'],
                ['q' => 'Какое направление flex по умолчанию?', 'o' => ['row', 'column', 'column-reverse', 'row-reverse'], 'c' => 1, 'e' => 'В RN flexDirection — column.'],
                ['q' => 'Как создать стили?', 'o' => ['CSS файлы', 'StyleSheet.create()', 'styled-components', 'Inline'], 'c' => 1, 'e' => 'StyleSheet.create().'],
            ],
            'Навигация и стейт' => [
                ['q' => 'Какая библиотека для навигации?', 'o' => ['React Router', 'React Navigation', 'Native Navigation', 'Mobile Router'], 'c' => 1, 'e' => 'React Navigation.'],
                ['q' => 'Как передать параметры?', 'o' => ['navigation.navigate(Screen, { id: 42 })', 'Props напрямую', 'Context только', 'Global state'], 'c' => 0, 'e' => 'Параметры через navigate.'],
                ['q' => 'Что такое Stack Navigator?', 'o' => ['Табы', 'Навигация стека push/pop', 'Drawer', 'Боковое меню'], 'c' => 1, 'e' => 'Stack Navigator — стек.'],
                ['q' => 'Как получить параметры экрана?', 'o' => ['this.props.params', 'route.params', 'useParams()', 'navigation.getParams()'], 'c' => 1, 'e' => 'Параметры через route.params.'],
                ['q' => 'Для чего Context?', 'o' => ['Навигация', 'Передача данных через дерево', 'Стили', 'API'], 'c' => 1, 'e' => 'Context без пропс-дриллинга.'],
            ],
            'Работа с API' => [
                ['q' => 'Какая функция для HTTP?', 'o' => ['XMLHttpRequest', 'fetch', 'axios', 'request'], 'c' => 1, 'e' => 'fetch — встроенная.'],
                ['q' => 'Что делает response.json()?', 'o' => ['Отправляет JSON', 'Парсит тело как JSON', 'Конвертирует', 'Создаёт JSON'], 'c' => 1, 'e' => 'Парсит JSON из тела.'],
                ['q' => 'Как обработать ошибку?', 'o' => ['if error', 'try/catch с async/await', '.catch()', 'error handler в URL'], 'c' => 1, 'e' => 'try/catch.'],
                ['q' => 'Зачем проверять response.ok?', 'o' => ['Для скорости', 'Проверка HTTP статуса 200-299', 'Для формата', 'Для размера'], 'c' => 1, 'e' => 'response.ok для 200-299.'],
                ['q' => 'Какая альтернатива fetch?', 'o' => ['jQuery', 'Axios', 'Request', 'SuperAgent'], 'c' => 1, 'e' => 'Axios.'],
            ],
            'Тест по Mobile Dev' => [
                ['q' => 'Какой компонент для загрузки?', 'o' => ['Loading', 'ActivityIndicator', 'Spinner', 'Progress'], 'c' => 1, 'e' => 'ActivityIndicator.'],
                ['q' => 'Какой стиль для компоновки?', 'o' => ['CSS Grid', 'Flexbox', 'Float', 'Position'], 'c' => 1, 'e' => 'Flexbox.'],
                ['q' => 'Как открыть модальное окно?', 'o' => ['navigation.navigate()', 'React Native Modal', 'Все варианты', 'navigation.push()'], 'c' => 2, 'e' => 'Можно Modal или push.'],
                ['q' => 'Как сохранить данные?', 'o' => ['AsyncStorage', 'localStorage', 'SessionStorage', 'Cache API'], 'c' => 0, 'e' => 'AsyncStorage.'],
                ['q' => 'Как обновить состояние списка?', 'o' => ['setState в useEffect', 'forceUpdate()', 'Пересоздать', 'jQuery'], 'c' => 0, 'e' => 'useState + useEffect.'],
            ],
        ],
        'practice' => [
            'Основы мобильной разработки' => [
                ['lang' => 'bash', 'title' => 'Создание проекта', 'prompt' => 'Создайте проект React Native.', 'out' => '', 'start' => '', 'tests' => [['contains', 'npx react-native init'], ['contains', 'run-android']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'javascript', 'title' => 'Структура проекта', 'prompt' => 'Создайте папки компонентов и экранов.', 'out' => '', 'start' => '', 'tests' => [['contains', 'mkdir'], ['contains', 'components']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'javascript', 'title' => 'Простой компонент', 'prompt' => 'Создайте HelloWorld с View и Text.', 'out' => '', 'start' => $startMobile2, 'tests' => [['contains', 'View'], ['contains', 'Text']], 'diff' => 'easy', 'time' => 15],
            ],
            'React Native компоненты' => [
                ['lang' => 'javascript', 'title' => 'Карточка товара', 'prompt' => 'Создайте карточку с Image и Text.', 'out' => '', 'start' => $startMobile2, 'tests' => [['contains', 'Image'], ['contains', 'Text']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'FlatList', 'prompt' => 'Создайте список с FlatList.', 'out' => '', 'start' => $startMobile2, 'tests' => [['contains', 'FlatList'], ['contains', 'renderItem']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'Форма ввода', 'prompt' => 'Создайте форму с TextInput.', 'out' => '', 'start' => $startMobile2, 'tests' => [['contains', 'TextInput'], ['contains', 'useState']], 'diff' => 'medium', 'time' => 20],
            ],
            'Навигация и стейт' => [
                ['lang' => 'javascript', 'title' => 'Stack Navigator', 'prompt' => 'Настройте навигацию между экранами.', 'out' => '', 'start' => $startMobileNav, 'tests' => [['contains', 'NavigationContainer'], ['contains', 'Stack.Navigator']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'Передача параметров', 'prompt' => 'Передайте ID на DetailsScreen.', 'out' => '', 'start' => $startMobileNav, 'tests' => [['contains', 'navigate'], ['contains', 'route.params']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'Tab Navigator', 'prompt' => 'Добавьте нижнюю навигацию.', 'out' => '', 'start' => '', 'tests' => [['contains', 'createBottomTabNavigator'], ['contains', 'Tab.Navigator']], 'diff' => 'hard', 'time' => 25],
            ],
            'Работа с API' => [
                ['lang' => 'javascript', 'title' => 'Fetch запрос', 'prompt' => 'Получите пользователей через fetch.', 'out' => '', 'start' => $startMobileApi, 'tests' => [['contains', 'fetch'], ['contains', 'response.json']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'Обработка ошибок', 'prompt' => 'Добавьте обработку ошибок к fetch.', 'out' => '', 'start' => $startMobileApi, 'tests' => [['contains', 'try'], ['contains', 'catch']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'Список с API', 'prompt' => 'Загрузите и отобразите список из API.', 'out' => '', 'start' => $startMobileApi, 'tests' => [['contains', 'useState'], ['contains', 'FlatList']], 'diff' => 'hard', 'time' => 25],
            ],
            'Тест по Mobile Dev' => [
                ['lang' => 'javascript', 'title' => 'Индикатор загрузки', 'prompt' => 'Добавьте ActivityIndicator.', 'out' => '', 'start' => $startMobileApi, 'tests' => [['contains', 'ActivityIndicator'], ['contains', 'loading']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'javascript', 'title' => 'Локальное хранилище', 'prompt' => 'Сохраните данные в AsyncStorage.', 'out' => '', 'start' => '', 'tests' => [['contains', 'AsyncStorage'], ['contains', 'setItem']], 'diff' => 'medium', 'time' => 20],
                ['lang' => 'javascript', 'title' => 'Pull-to-refresh', 'prompt' => 'Реализуйте обновление свайпом.', 'out' => '', 'start' => '', 'tests' => [['contains', 'onRefresh'], ['contains', 'FlatList']], 'diff' => 'hard', 'time' => 25],
            ],
        ]
    ],

    20 => [
        'lessons' => [
            'Алфавит и произношение' => '<h2>Алфавит и произношение</h2><p>Английский алфавит — 26 букв. Правильное произношение — основа общения.</p><h3>Алфавит</h3><pre><code>A B C D E F G H I J K L M N O P Q R S T U V W X Y Z</code></pre><h3>Гласные и согласные</h3><p><strong>Vowels:</strong> A, E, I, O, U</p><p><strong>Consonants:</strong> все остальные</p><h3>Основные звуки</h3><ul><li><strong>Short vowels</strong> — cat, bed, sit</li><li><strong>Long vowels</strong> — cake, feet, bike</li><li><strong>Diphthongs</strong> — boy, now</li></ul><h3>Типичные ошибки</h3><ul><li>TH — /θ/ (think) и /ð/ (this)</li><li>W — /w/, не /в/</li><li>R — американский /r/</li></ul>',
            'Базовая грамматика' => '<h2>Базовая грамматика</h2><p>Артикли, to be, Simple Present, множественное число.</p><h3>Артикли</h3><pre><code>a book — неопределённый\\nan apple — перед гласным\\nthe book — определённый</code></pre><h3>To be</h3><pre><code>I am a student.\\nYou are my friend.\\nHe is tall.\\nShe is a teacher.\\nWe are developers.\\nThey are friends.</code></pre><h3>Simple Present</h3><pre><code>I work. / He works. (-s)\\nDo you work? Does he work?\\nI dont work. He doesnt work.</code></pre><h3>Множественное число</h3><pre><code>book - books\\nbox - boxes\\nchild - children\\nman - men</code></pre>',
            'IT-терминология' => '<h2>IT-терминология</h2><p>Базовые IT-термины для работы в международных командах.</p><h3>Термины</h3><pre><code>Computer - компьютер\\nSoftware - ПО\\nHardware - железо\\nCode - код\\nDebug - отлаживать\\nBug - ошибка\\nFeature - функциональность\\nDeploy - развёртывание\\nServer - сервер\\nDatabase - БД\\nAPI - интерфейс\\nFrontend - клиентская часть\\nBackend - серверная часть</code></pre><h3>Аббревиатуры</h3><ul><li><strong>HTML</strong> - HyperText Markup Language</li><li><strong>CSS</strong> - Cascading Style Sheets</li><li><strong>JSON</strong> - JavaScript Object Notation</li><li><strong>SQL</strong> - Structured Query Language</li><li><strong>REST</strong> - Representational State Transfer</li></ul><h3>Фразы</h3><pre><code>Can you review my code?\\nI found a bug.\\nLets deploy to production.\\nThe build is broken.\\nI will fix it today.</code></pre>',
            'Чтение документации' => '<h2>Чтение документации</h2><p>Умение читать документацию — ключевой навык.</p><h3>Структура</h3><ul><li><strong>Overview</strong> — обзор</li><li><strong>Getting Started</strong> — начало</li><li><strong>API Reference</strong> — справочник</li><li><strong>Examples</strong> — примеры</li></ul><h3>Фразы</h3><pre><code>Returns - возвращает\\nParameters - параметры\\nOptional - необязательный\\nRequired - обязательный\\nDefault - по умолчанию\\nDeprecated - устаревший\\nSee also - смотрите также</code></pre><h3>Пример</h3><pre><code>function fetchUsers(options?)\\nParameters:\\n  options.limit (number, optional) - Default: 10\\n  options.offset (number, optional) - Default: 0\\nReturns:\\n  Promise&lt;User[]&gt;</code></pre>',
            'Тест по English A1' => '<h2>Тест по English A1</h2><p>Проверьте знания по алфавиту, грамматике, IT-терминологии и чтению документации.</p>',
        ],
        'quizzes' => [
            'Алфавит и произношение' => [
                ['q' => 'Сколько букв в алфавите?', 'o' => ['24', '26', '28', '30'], 'c' => 1, 'e' => '26 букв.'],
                ['q' => 'Какие буквы гласные?', 'o' => ['A, E, I, O, U', 'A, B, C, D, E', 'A, I, U, E, O', 'Все'], 'c' => 0, 'e' => 'A, E, I, O, U.'],
                ['q' => 'Как произносится TH в think?', 'o' => ['/t/', '/θ/', '/ð/', '/s/'], 'c' => 1, 'e' => '/θ/ межзубный.'],
                ['q' => 'Что такое stress?', 'o' => ['Стресс', 'Ударение на слог', 'Давление', 'Напряжение'], 'c' => 1, 'e' => 'Ударение.'],
                ['q' => 'Какой звук отсутствует в русском?', 'o' => ['/k/', '/θ/', '/m/', '/p/'], 'c' => 1, 'e' => '/θ/ отсутствует.'],
            ],
            'Базовая грамматика' => [
                ['q' => 'Когда использовать an?', 'o' => ['Перед согласным', 'Перед гласным звуком', 'Перед существительным', 'Всегда'], 'c' => 1, 'e' => 'Перед гласным.'],
                ['q' => 'He work или He works?', 'o' => ['He work', 'He works', 'Оба', 'He working'], 'c' => 1, 'e' => 'He works.'],
                ['q' => 'Вопрос с to be?', 'o' => ['Do you are?', 'Are you a student?', 'You are?', 'Is you?'], 'c' => 1, 'e' => 'To be перед подлежащим.'],
                ['q' => 'Множественное от box?', 'o' => ['boxs', 'boxes', 'boxies', 'boxs'], 'c' => 1, 'e' => 'boxes.'],
                ['q' => 'Отрицание в Simple Present?', 'o' => ['I not work', 'I dont work', 'I doesnt work', 'I no work'], 'c' => 1, 'e' => 'I dont. He doesnt.'],
            ],
            'IT-терминология' => [
                ['q' => 'Что означает debug?', 'o' => ['Удалять', 'Искать и исправлять ошибки', 'Отлаживать железо', 'Удалять код'], 'c' => 1, 'e' => 'Поиск и исправление ошибок.'],
                ['q' => 'Что такое API?', 'o' => ['Advanced Programming Interface', 'Application Programming Interface', 'Automated Process', 'Advanced Protocol'], 'c' => 1, 'e' => 'Application Programming Interface.'],
                ['q' => 'Что означает deploy?', 'o' => ['Удалять', 'Развёртывание', 'Компилировать', 'Тестировать'], 'c' => 1, 'e' => 'Развёртывание.'],
                ['q' => 'Что такое frontend?', 'o' => ['Серверная часть', 'Клиентская часть', 'База данных', 'Сеть'], 'c' => 1, 'e' => 'Клиентская часть.'],
                ['q' => 'Что означает deprecated?', 'o' => ['Новый', 'Устаревший', 'Обязательный', 'Ошибочный'], 'c' => 1, 'e' => 'Устаревший.'],
            ],
            'Чтение документации' => [
                ['q' => 'Где начать изучение?', 'o' => ['API Reference', 'README.md', 'FAQ', 'Changelog'], 'c' => 1, 'e' => 'README.md.'],
                ['q' => 'Что означает optional?', 'o' => ['Обязательный', 'Необязательный', 'Опциональная библиотека', 'Альтернативный'], 'c' => 1, 'e' => 'Необязательный.'],
                ['q' => 'Что такое Parameters?', 'o' => ['Результаты', 'Входные данные', 'Ошибки', 'Примеры'], 'c' => 1, 'e' => 'Входные данные.'],
                ['q' => 'Что означает Returns?', 'o' => ['Возвращает значение', 'Возвращает ошибку', 'Возвращает список', 'Возвращает промис'], 'c' => 0, 'e' => 'Возвращает значение.'],
                ['q' => 'Как найти информацию?', 'o' => ['Читать всё', 'Ctrl+F', 'В чате', 'Видео'], 'c' => 1, 'e' => 'Поиск.'],
            ],
            'Тест по English A1' => [
                ['q' => 'Как написать компьютер?', 'o' => ['Computer', 'Computor', 'Kompyuter', 'Computar'], 'c' => 0, 'e' => 'Computer.'],
                ['q' => 'Что означает I am a developer?', 'o' => ['Я разработчик', 'Я developments', 'Я develop', 'Я development'], 'c' => 0, 'e' => 'Я разработчик.'],
                ['q' => 'Аббревиатура для JSON?', 'o' => ['JSN', 'JSON', 'JAS', 'JSO'], 'c' => 1, 'e' => 'JSON.'],
                ['q' => 'She doesnt или She dont work?', 'o' => ['She dont work', 'She doesnt work', 'Оба', 'She not work'], 'c' => 1, 'e' => 'doesnt для he/she/it.'],
                ['q' => 'Что такое README?', 'o' => ['Ошибки', 'Информация о проекте', 'Конфигурация', 'Тесты'], 'c' => 1, 'e' => 'Описание проекта.'],
            ],
        ],
        'practice' => [
            'Алфавит и произношение' => [
                ['lang' => 'text', 'title' => 'Транскрипция', 'prompt' => 'Напишите транскрипцию: computer, developer, function.', 'out' => '', 'start' => '', 'tests' => [['contains', 'computer'], ['contains', 'developer'], ['contains', 'function']], 'diff' => 'easy', 'time' => 15],
                ['lang' => 'text', 'title' => 'Минимальные пары', 'prompt' => 'Различия: ship/sheep, bit/beat, full/fool.', 'out' => '', 'start' => '', 'tests' => [['contains', 'ship'], ['contains', 'sheep'], ['contains', 'bit']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'text', 'title' => 'Стресс слов', 'prompt' => 'Ударение: record, present, address.', 'out' => '', 'start' => '', 'tests' => [['contains', 'record'], ['contains', 'present'], ['contains', 'address']], 'diff' => 'medium', 'time' => 15],
            ],
            'Базовая грамматика' => [
                ['lang' => 'text', 'title' => 'Артикли', 'prompt' => 'Вставьте a, an или the.', 'out' => '', 'start' => '', 'tests' => [['contains', 'a cat'], ['contains', 'an orange']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'text', 'title' => 'To Be', 'prompt' => 'Дополните: I _ a student. She _ my friend.', 'out' => '', 'start' => '', 'tests' => [['contains', 'am'], ['contains', 'is']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'text', 'title' => 'Simple Present', 'prompt' => 'Напишите отрицание: He works every day.', 'out' => '', 'start' => '', 'tests' => [['contains', 'doesnt'], ['contains', 'Does he']], 'diff' => 'medium', 'time' => 15],
            ],
            'IT-терминология' => [
                ['lang' => 'text', 'title' => 'Перевод терминов', 'prompt' => 'Переведите: frontend, backend, debug, deploy.', 'out' => '', 'start' => '', 'tests' => [['contains', 'frontend'], ['contains', 'backend'], ['contains', 'debug']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'text', 'title' => 'Аббревиатуры', 'prompt' => 'Расшифруйте: HTML, CSS, JSON, SQL.', 'out' => '', 'start' => '', 'tests' => [['contains', 'HyperText'], ['contains', 'Cascading'], ['contains', 'JavaScript']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'text', 'title' => 'IT фразы', 'prompt' => 'Переведите: I found a bug. Lets deploy.', 'out' => '', 'start' => '', 'tests' => [['contains', 'found a bug'], ['contains', 'deploy'], ['contains', 'production']], 'diff' => 'medium', 'time' => 15],
            ],
            'Чтение документации' => [
                ['lang' => 'text', 'title' => 'Чтение API', 'prompt' => 'Прочитайте fetchUsers и ответьте.', 'out' => '', 'start' => '', 'tests' => [['contains', 'limit'], ['contains', 'offset'], ['contains', 'Promise']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'text', 'title' => 'Поиск информации', 'prompt' => 'Найдите useState в документации.', 'out' => '', 'start' => '', 'tests' => [['contains', 'useState'], ['contains', 'initialState']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'text', 'title' => 'README проекта', 'prompt' => 'Прочитайте README и опишите установку.', 'out' => '', 'start' => '', 'tests' => [['contains', 'install'], ['contains', 'npm']], 'diff' => 'easy', 'time' => 10],
            ],
            'Тест по English A1' => [
                ['lang' => 'text', 'title' => 'Диалог', 'prompt' => 'Переведите диалог о профессии.', 'out' => '', 'start' => '', 'tests' => [['contains', 'developer'], ['contains', 'JavaScript']], 'diff' => 'easy', 'time' => 10],
                ['lang' => 'text', 'title' => 'Письмо', 'prompt' => 'Напишите письмо-знакомство.', 'out' => '', 'start' => '', 'tests' => [['contains', 'My name'], ['contains', 'I am'], ['contains', 'developer']], 'diff' => 'medium', 'time' => 15],
                ['lang' => 'text', 'title' => 'Технический текст', 'prompt' => 'Прочитайте абзац о React.', 'out' => '', 'start' => '', 'tests' => [['contains', 'React'], ['contains', 'user interface'], ['contains', 'library']], 'diff' => 'medium', 'time' => 15],
            ],
        ]
    ],
];