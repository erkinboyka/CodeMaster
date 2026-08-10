<?php

return [
    16 => [ // TypeScript
        'lessons' => [
            ['title' => 'Основы TypeScript: типы и интерфейсы', 'type' => 'video', 'module' => 'TypeScript основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Примитивные типы, массивы, кортежи, алиасы, интерфейсы, функциональные типы.',
             'content' => '<h2>Основы TypeScript: типы и интерфейсы</h2>
<h3>Примитивные типы данных</h3>
<p>TypeScript добавляет строгую типизацию к JavaScript, помогая обнаруживать ошибки на ранних этапах. Все примитивные типы JavaScript доступны в TypeScript с дополнительными возможностями.</p>
<pre><code>// Примитивные типы
let username: string = "Алексей";
let age: number = 25;
let isActive: boolean = true;
let nothing: null = null;
let notDefined: undefined = undefined;

// void — для функций, ничего не возвращающих
function logMessage(message: string): void {
    console.log(message);
}

// never — для функций, никогда не завершающихся
function throwError(message: string): never {
    throw new Error(message);
}

// any — отключение проверки типов (использовать осторожно!)
let dynamicValue: any = "может быть что угодно";
dynamicValue = 42;

// unknown — безопасная альтернатива any
let userInput: unknown = getExternalData();
if (typeof userInput === "string") {
    console.log(userInput.toUpperCase());
}</code></pre>
<h3>Массивы и кортежи</h3>
<p>TypeScript предоставляет два способа типизации массивов и поддерживает кортежи — фиксированные по длине массивы с известными типами.</p>
<pre><code>// Типизация массивов
let numbers: number[] = [1, 2, 3, 4, 5];
let strings: Array&lt;string&gt; = ["a", "b", "c"];

// Кортежи
let person: [string, number] = ["Иван", 30];
let record: [string, number, boolean] = ["запись", 123, true];

// Кортеж с необязательными элементами
let flexible: [string, number?] = ["только имя"];</code></pre>
<h3>Типы объектов и алиасы</h3>
<pre><code>// Тип объекта
let user: { name: string; age: number; email: string } = {
    name: "Мария", age: 28, email: "maria@example.com"
};

// Алиасы типов
type UserID = string | number;
type Status = "active" | "inactive" | "pending";
type User = { id: UserID; name: string; status: Status; };</code></pre>
<h3>Интерфейсы</h3>
<pre><code>// Базовый интерфейс
interface Animal {
    name: string;
    sound: string;
    legs: number;
}

// Необязательные и readonly свойства
interface Config {
    readonly apiUrl: string;
    timeout?: number;
    retries: number;
}

// Наследование
interface Dog extends Animal {
    breed: string;
    isGoodBoy: boolean;
}</code></pre>
<h3>Функциональные типы</h3>
<pre><code>type MathOperation = (a: number, b: number) =&gt; number;
const add: MathOperation = (a, b) =&gt; a + b;

// Необязательные и параметры по умолчанию
function greet(name: string, greeting?: string): string {
    return `${greeting || "Привет"}, ${name}!`;
}

// Rest параметры
function sum(...numbers: number[]): number {
    return numbers.reduce((total, n) =&gt; total + n, 0);
}

// Type assertions
let input: unknown = "Hello World";
let length: number = (input as string).length;</code></pre>
<h3>Сравнение interface и type</h3>
<table>
<tr><th>Характеристика</th><th>interface</th><th>type</th></tr>
<tr><td>Наследование</td><td>extends</td><td>交叉ение (&amp;)</td></tr>
<tr><td>Объединение типов</td><td>Нет</td><td>Да (union)</td></tr>
<tr><td>Переопределение</td><td>Да (merge)</td><td>Нет</td></tr>
<tr><td>Примитивные типы</td><td>Нет</td><td>Да</td></tr>
</table>
<h3>Лучшие практики</h3>
<ul>
<li>Избегайте <code>any</code> — предпочитайте <code>unknown</code> или конкретные типы</li>
<li>Используйте интерфейсы для структуры объектов и контрактов классов</li>
<li>Используйте type aliases для union типов и составных типов</li>
<li>Всегда указывайте типы возвращаемых значений функций</li>
<li>Используйте readonly для неизменяемых свойств</li>
</ul>'],

            ['title' => 'Продвинутые типы', 'type' => 'video', 'module' => 'TypeScript продвинутый', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Дженерики, утилитарные типы, условные типы, discriminated unions.',
             'content' => '<h2>Продвинутые типы TypeScript</h2>
<h3>Обобщённые типы (Generics)</h3>
<p>Generics позволяют создавать переиспользуемый код, работающий с различными типами, сохраняя типобезопасность.</p>
<pre><code>// Простая обобщённая функция
function identity&lt;T&gt;(arg: T): T {
    return arg;
}

// Обобщённый интерфейс
interface ApiResponse&lt;T&gt; {
    data: T;
    status: number;
    message: string;
}

// Обобщённый класс
class Box&lt;T&gt; {
    private value: T;
    constructor(value: T) { this.value = value; }
    getValue(): T { return this.value; }
}</code></pre>
<h3>Ограничения (Constraints)</h3>
<pre><code>interface HasLength {
    length: number;
}

function logLength&lt;T extends HasLength&gt;(arg: T): T {
    console.log(`Длина: ${arg.length}`);
    return arg;
}

// Ограничение по keyof
function getProperty&lt;T, K extends keyof T&gt;(obj: T, key: K): T[K] {
    return obj[key];
}</code></pre>
<h3>Утилитарные типы</h3>
<pre><code>interface User { id: number; name: string; email: string; age: number; }

type PartialUser = Partial&lt;User&gt;;       // все свойства необязательны
type RequiredUser = Required&lt;PartialUser&gt;; // все свойства обязательны
type UserBasic = Pick&lt;User, "id" | "name"&gt;;
type UserNoEmail = Omit&lt;User, "email"&gt;;
type ReadonlyUser = Readonly&lt;User&gt;;

// Exclude и Extract
type AllTypes = string | number | boolean | null;
type NoNull = Exclude&lt;AllTypes, null&gt;;
type OnlyNull = Extract&lt;AllTypes, null&gt;;

// ReturnType и Parameters
function createUser(name: string, age: number): User {
    return { id: 1, name, email: "", age };
}
type Ret = ReturnType&lt;typeof createUser&gt;;
type Params = Parameters&lt;typeof createUser&gt;;</code></pre>
<h3>Условные типы и infer</h3>
<pre><code>type IsString&lt;T&gt; = T extends string ? "yes" : "no";
type A = IsString&lt;string&gt;;  // "yes"
type B = IsString&lt;number&gt;;  // "no"

type UnpackPromise&lt;T&gt; = T extends Promise&lt;infer U&gt; ? U : T;
type Result = UnpackPromise&lt;Promise&lt;string&gt;&gt;; // string</code></pre>
<h3>Template Literal Types</h3>
<pre><code>type Color = "red" | "blue" | "green";
type Size = "small" | "medium" | "large";
type ColorSize = `${Color}-${Size}`;
// "red-small" | "red-medium" | ...</code></pre>
<h3>Discriminated Unions и Type Guards</h3>
<pre><code>interface Circle { kind: "circle"; radius: number; }
interface Square { kind: "square"; sideLength: number; }
type Shape = Circle | Square;

function getArea(shape: Shape): number {
    switch (shape.kind) {
        case "circle": return Math.PI * shape.radius ** 2;
        case "square": return shape.sideLength ** 2;
    }
}

// Custom Type Guard
function isCircle(shape: Shape): shape is Circle {
    return shape.kind === "circle";
}</code></pre>
<h3>Mapped Types</h3>
<pre><code>type Nullable&lt;T&gt; = { [K in keyof T]: T[K] | null };

interface Product { name: string; price: number; }
type NullableProduct = Nullable&lt;Product&gt;;
// { name: string | null; price: number | null; }</code></pre>
<h3>Лучшие практики</h3>
<ul>
<li>Используйте generics для переиспользуемых компонентов и утилит</li>
<li>Применяйте constraints для ограничения generic типов</li>
<li>Используйте встроенные утилитарные типы вместо собственных</li>
<li>Применяйте discriminated unions для безопасной работы с вариантами</li>
<li>Используйте type guards для безопасного приведения типов</li>
</ul>'],

            ['title' => 'Классы и модули в TypeScript', 'type' => 'video', 'module' => 'TypeScript классы', 'difficulty' => 'medium', 'duration_minutes' => 28,
             'description' => 'Классы, модификаторы доступа, abstract, enum, модули и пространства имён.',
             'content' => '<h2>Классы и модули TypeScript</h2>
<h3>Основы классов</h3>
<pre><code>class Animal {
    name: string;
    protected species: string;
    private _id: number;

    constructor(name: string, species: string, id: number) {
        this.name = name;
        this.species = species;
        this._id = id;
    }

    speak(): string { return `${this.name} издаёт звук`; }
    get id(): number { return this._id; }
}

class Dog extends Animal {
    breed: string;
    constructor(name: string, breed: string, id: number) {
        super(name, "Canis", id);
        this.breed = breed;
    }
    speak(): string { return `${this.name} лает`; }
    fetch(item: string): string { return `${this.name} приносит ${item}`; }
}</code></pre>
<h3>Модификаторы доступа</h3>
<table>
<tr><th>Модификатор</th><th>В классе</th><th>В наследнике</th><th>Вне класса</th></tr>
<tr><td>public</td><td>Да</td><td>Да</td><td>Да</td></tr>
<tr><td>protected</td><td>Да</td><td>Да</td><td>Нет</td></tr>
<tr><td>private</td><td>Да</td><td>Нет</td><td>Нет</td></tr>
</table>
<pre><code>class Employee {
    public name: string;
    protected department: string;
    private salary: number;
    readonly companyId: string;

    constructor(name: string, dept: string, salary: number) {
        this.name = name;
        this.department = dept;
        this.salary = salary;
        this.companyId = "COMP-001";
    }
    getSalary(): number { return this.salary; }
}</code></pre>
<h3>Абстрактные классы</h3>
<pre><code>abstract class Shape {
    abstract readonly color: string;
    abstract getArea(): number;
    abstract getPerimeter(): number;
    describe(): string { return `Фигура: ${this.constructor.name}, цвет: ${this.color}`; }
}

class Rectangle extends Shape {
    readonly color: string;
    constructor(private width: number, private height: number, color: string) {
        super(); this.color = color;
    }
    getArea(): number { return this.width * this.height; }
    getPerimeter(): number { return 2 * (this.width + this.height); }
}</code></pre>
<h3>Статические члены</h3>
<pre><code>class MathUtils {
    static readonly PI = 3.14159265358979;
    static clamp(value: number, min: number, max: number): number {
        return Math.min(Math.max(value, min), max);
    }
}

// Синглтон
class Database {
    private static instance: Database;
    private constructor() {}
    static getInstance(): Database {
        if (!Database.instance) Database.instance = new Database();
        return Database.instance;
    }
}</code></pre>
<h3>Перечисления (Enums)</h3>
<pre><code>enum Direction { Up, Down, Left, Right }
enum Status { Active = "ACTIVE", Inactive = "INACTIVE", Pending = "PENDING" }
enum HttpStatus { OK = 200, NotFound = 404, ServerError = 500 }

const enum Color { Red, Green, Blue } // оптимизация при компиляции
let c: Color = Color.Red;</code></pre>
<h3>implements — реализация интерфейсов</h3>
<pre><code>interface Serializable { serialize(): string; }
interface Loggable { log(): void; }

class UserModel implements Serializable, Loggable {
    constructor(private name: string, private email: string) {}
    serialize(): string { return JSON.stringify({ name: this.name, email: this.email }); }
    log(): void { console.log(`User: ${this.name}`); }
}</code></pre>
<h3>Модульная система</h3>
<pre><code>// model.ts
export interface User { id: number; name: string; }
export function createUser(name: string): User { return { id: Date.now(), name }; }

// service.ts
import { User, createUser } from "./model";

// Re-export
export { User } from "./model";

// Динамический импорт
async function loadFeature() {
    const module = await import("./heavy-module");
    module.init();
}</code></pre>
<h3>Лучшие практики</h3>
<ul>
<li>Используйте приватные поля для инкапсуляции</li>
<li>Применяйте readonly для неизменяемых свойств</li>
<li>Используйте абстрактные классы для общих контрактов</li>
<li>Предпочитайте интерфейсы классам при возможности</li>
<li>Используйте const enum для оптимизации размера бандла</li>
</ul>'],

            ['title' => 'TypeScript с React', 'type' => 'video', 'module' => 'TypeScript React', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'Типизация пропсов, хуков, событий, контекста и форм в React с TypeScript.',
             'content' => '<h2>TypeScript с React</h2>
<h3>Типизация компонентов</h3>
<pre><code>// interface для пропсов — рекомендуется
interface ButtonProps {
    label: string;
    onClick: () =&gt; void;
    variant?: "primary" | "secondary" | "danger";
    disabled?: boolean;
    size?: "small" | "medium" | "large";
}

function Button({ label, onClick, variant = "primary", disabled = false, size = "medium" }: ButtonProps) {
    return (
        &lt;button className={`btn btn-${variant} btn-${size}`} onClick={onClick} disabled={disabled}&gt;
            {label}
        &lt;/button&gt;
    );
}</code></pre>
<h3>Типизация children</h3>
<pre><code>import { ReactNode, PropsWithChildren } from "react";

interface CardProps { title: string; children: ReactNode; }
function Card({ title, children }: CardProps) {
    return (&lt;div className="card"&gt;&lt;h3&gt;{title}&lt;/h3&gt;&lt;div&gt;{children}&lt;/div&gt;&lt;/div&gt;);
}

// PropsWithChildren — автоматически добавляет children
interface ModalProps { isOpen: boolean; onClose: () =&gt; void; }
function Modal({ isOpen, onClose, children }: PropsWithChildren&lt;ModalProps&gt;) {
    if (!isOpen) return null;
    return (&lt;div className="modal"&gt;&lt;button onClick={onClose}&gt;×&lt;/button&gt;{children}&lt;/div&gt;);
}</code></pre>
<h3>Типизация событий</h3>
<pre><code>import { MouseEvent, ChangeEvent, FormEvent, KeyboardEvent } from "react";

function Form({ onSubmit }: { onSubmit: (data: FormData) =&gt; void }) {
    const handleClick = (e: MouseEvent&lt;HTMLButtonElement&gt;) =&gt; {
        console.log(e.currentTarget.textContent);
    };
    const handleChange = (e: ChangeEvent&lt;HTMLInputElement&gt;) =&gt; {
        console.log(e.target.value);
    };
    const handleSubmit = (e: FormEvent&lt;HTMLFormElement&gt;) =&gt; {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        onSubmit(formData);
    };
    const handleKeyPress = (e: KeyboardEvent&lt;HTMLInputElement&gt;) =&gt; {
        if (e.key === "Enter") { /* обработка */ }
    };
    return (
        &lt;form onSubmit={handleSubmit}&gt;
            &lt;input onChange={handleChange} onKeyPress={handleKeyPress} /&gt;
            &lt;button type="submit"&gt;Submit&lt;/button&gt;
        &lt;/form&gt;
    );
}</code></pre>
<h3>Хуки с типизацией</h3>
<pre><code>interface User { id: number; name: string; email: string; }

function UserProfile() {
    const [user, setUser] = useState&lt;User | null&gt;(null);
    const [loading, setLoading] = useState&lt;boolean&gt;(true);
    const [error, setStateError] = useState&lt;string | null&gt;(null);

    const inputRef = useRef&lt;HTMLInputElement&gt;(null);
    const timerRef = useRef&lt;NodeJS.Timeout | null&gt;(null);

    useEffect(() =&gt; {
        async function fetchUser() {
            try {
                const response = await fetch("/api/user");
                const data: User = await response.json();
                setUser(data);
            } catch (err) {
                setStateError(err instanceof Error ? err.message : "Unknown error");
            } finally {
                setLoading(false);
            }
        }
        fetchUser();
    }, []);

    if (loading) return &lt;div&gt;Загрузка...&lt;/div&gt;;
    if (error) return &lt;div&gt;Ошибка: {error}&lt;/div&gt;;
    return (&lt;div&gt;&lt;input ref={inputRef} /&gt;&lt;h1&gt;{user?.name}&lt;/h1&gt;&lt;/div&gt;);
}</code></pre>
<h3>Кастомные хуки с generics</h3>
<pre><code>interface UseFetchResult&lt;T&gt; { data: T | null; loading: boolean; error: string | null; refetch: () =&gt; void; }

function useFetch&lt;T&gt;(url: string): UseFetchResult&lt;T&gt; {
    const [data, setData] = useState&lt;T | null&gt;(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState&lt;string | null&gt;(null);

    const fetchData = useCallback(async () =&gt; {
        setLoading(true); setError(null);
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error("Ошибка загрузки");
            setData(await response.json());
        } catch (err) {
            setError(err instanceof Error ? err.message : "Unknown error");
        } finally { setLoading(false); }
    }, [url]);

    useEffect(() =&gt; { fetchData(); }, [fetchData]);
    return { data, loading, error, refetch: fetchData };
}

// useLocalStorage
function useLocalStorage&lt;T&gt;(key: string, initialValue: T): [T, (v: T | ((prev: T) =&gt; T)) =&gt; void] {
    const [storedValue, setStoredValue] = useState&lt;T&gt;(() =&gt; {
        try {
            const item = window.localStorage.getItem(key);
            return item ? JSON.parse(item) : initialValue;
        } catch { return initialValue; }
    });
    const setValue = (value: T | ((prev: T) =&gt; T)) =&gt; {
        setStoredValue(prev =&gt; {
            const val = value instanceof Function ? value(prev) : value;
            window.localStorage.setItem(key, JSON.stringify(val));
            return val;
        });
    };
    return [storedValue, setValue];
}</code></pre>
<h3>Context API с TypeScript</h3>
<pre><code>interface AuthContextType {
    user: User | null;
    isAuthenticated: boolean;
    login: (email: string, password: string) =&gt; Promise&lt;void&gt;;
    logout: () =&gt; void;
}

const AuthContext = createContext&lt;AuthContextType | undefined&gt;(undefined);

function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState&lt;User | null&gt;(null);
    const login = async (email: string, password: string) =&gt; {
        const res = await fetch("/api/login", { method: "POST", body: JSON.stringify({ email, password }) });
        setUser(await res.json());
    };
    const logout = () =&gt; setUser(null);
    return (
        &lt;AuthContext.Provider value={{ user, isAuthenticated: !!user, login, logout }}&gt;
            {children}
        &lt;/AuthContext.Provider&gt;
    );
}

function useAuth(): AuthContextType {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error("useAuth должен использоваться внутри AuthProvider");
    return ctx;
}</code></pre>
<h3>Generic компоненты</h3>
<pre><code>interface ListProps&lt;T&gt; {
    items: T[];
    renderItem: (item: T) =&gt; ReactNode;
    keyExtractor: (item: T) =&gt; string;
    emptyMessage?: string;
}

function List&lt;T&gt;({ items, renderItem, keyExtractor, emptyMessage = "Нет элементов" }: ListProps&lt;T&gt;) {
    if (items.length === 0) return &lt;div&gt;{emptyMessage}&lt;/div&gt;;
    return (&lt;ul&gt;{items.map(item =&gt; (&lt;li key={keyExtractor(item)}&gt;{renderItem(item)}&lt;/li&gt;))}&lt;/ul&gt;);
}</code></pre>
<h3>Лучшие практики</h3>
<ul>
<li>Используйте interface для пропсов — он расширяемый</li>
<li>Типизируйте event handlers с React типами (MouseEvent, ChangeEvent)</li>
<li>Для useRef с DOM используйте <code>useRef&lt;HTMLElement&gt;(null)</code></li>
<li>Создавайте кастомные хуки с generics для переиспользования</li>
<li>Используйте discriminated unions для состояний компонентов</li>
<li>Экспортируйте типы пропсов для переиспользования</li>
</ul>'],

            ['title' => 'Тест по TypeScript', 'type' => 'quiz', 'module' => 'TypeScript тест', 'difficulty' => 'hard', 'duration_minutes' => 15,
             'description' => 'Итоговый тест по TypeScript.',
             'content' => '<h2>Тест по TypeScript</h2>'],
        ],
    ],

    17 => [ // Docker
        'lessons' => [
            ['title' => 'Основы Docker: контейнеры и образы', 'type' => 'video', 'module' => 'Docker основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Что такое Docker, контейнеры vs VM, архитектура, установка и основные команды.',
             'content' => '<h2>Основы Docker: контейнеры и образы</h2>
<h3>Что такое Docker?</h3>
<p>Docker — платформа контейнеризации, которая упаковывает приложение со всеми зависимостями в стандартную единицу — контейнер. Контейнеры работают на любом компьютере с Docker, обеспечивая одинаковое поведение в разработке, тестировании и продакшене.</p>
<h3>Контейнеры vs Виртуальные машины</h3>
<table>
<tr><th>Параметр</th><th>Контейнеры</th><th>Виртуальные машины</th></tr>
<tr><td>Уровень виртуализации</td><td>Уровень ОС (общее ядро)</td><td>Аппаратный уровень</td></tr>
<tr><td>Размер образа</td><td>Мегабайты</td><td>Гигабайты</td></tr>
<tr><td>Скорость запуска</td><td>Секунды</td><td>Минуты</td></tr>
<tr><td>Изоляция</td><td>Процессная изоляция</td><td>Полная изоляция ОС</td></tr>
<tr><td>Производительность</td><td>Близка к нативной</td><td>Зависимость от гипервизора</td></tr>
</table>
<h3>Архитектура Docker</h3>
<ul>
<li><strong>Docker Daemon (dockerd)</strong> — фоновый процесс, управляющий контейнерами, образами, сетями и томами</li>
<li><strong>Docker Client</strong> — CLI-интерфейс для взаимодействия с демоном через REST API</li>
<li><strong>Docker Registry</strong> — хранилище образов (Docker Hub, приватные реестры)</li>
</ul>
<h3>Установка и проверка</h3>
<pre><code>docker --version
docker run hello-world</code></pre>
<h3>Основные команды Docker</h3>
<p>Работа с контейнерами:</p>
<pre><code># Запуск контейнера
docker run -d -p 8080:80 --name my-nginx nginx

# Просмотр контейнеров
docker ps                       # запущенные
docker ps -a                    # все

# Управление
docker stop my-nginx
docker rm my-nginx</code></pre>
<p>Работа с образами:</p>
<pre><code>docker images                    # список образов
docker pull ubuntu:22.04         # скачивание
docker rmi nginx:latest          # удаление
docker build -t my-app:1.0 .     # сборка</code></pre>
<h3>Полезные опции</h3>
<ul>
<li><code>-p host_port:container_port</code> — маппинг портов</li>
<li><code>-e KEY=VALUE</code> — переменные окружения</li>
<li><code>--name</code> — имя контейнера</li>
<li><code>-d</code> — фоновый режим</li>
<li><code>-it</code> — интерактивный режим с терминалом</li>
</ul>
<h3>Отладка и мониторинг</h3>
<pre><code>docker logs my-nginx                      # логи
docker exec -it my-nginx /bin/bash        # shell в контейнере
docker inspect my-nginx                   # информация</code></pre>
<h3>Docker Registry</h3>
<pre><code>docker login                              # авторизация
docker push username/my-app:1.0           # публикация
docker search python                      # поиск</code></pre>'],

            ['title' => 'Написание Dockerfile', 'type' => 'video', 'module' => 'Docker Dockerfile', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Инструкции Dockerfile, multi-stage builds, .dockerignore, оптимизация и best practices.',
             'content' => '<h2>Написание Dockerfile</h2>
<h3>Что такое Dockerfile?</h3>
<p>Dockerfile — текстовый файл с инструкциями для автоматической сборки Docker-образа. Каждая инструкция создаёт новый слой, влияющий на размер и кэширование.</p>
<h3>Структура Dockerfile</h3>
<pre><code>FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
EXPOSE 3000
CMD ["node", "server.js"]</code></pre>
<h3>Основные инструкции</h3>
<table>
<tr><th>Инструкция</th><th>Описание</th><th>Пример</th></tr>
<tr><td>FROM</td><td>Базовый образ</td><td>FROM node:18-alpine</td></tr>
<tr><td>WORKDIR</td><td>Рабочая директория</td><td>WORKDIR /app</td></tr>
<tr><td>COPY</td><td>Копирование файлов</td><td>COPY . .</td></tr>
<tr><td>ADD</td><td>Копирование с распаковкой</td><td>ADD archive.tar.gz /app</td></tr>
<tr><td>RUN</td><td>Команда при сборке</td><td>RUN npm install</td></tr>
<tr><td>EXPOSE</td><td>Объявление порта</td><td>EXPOSE 8080</td></tr>
<tr><td>CMD</td><td>Команда запуска</td><td>CMD ["nginx", "-g", "daemon off;"]</td></tr>
<tr><td>ENTRYPOINT</td><td>Точка входа</td><td>ENTRYPOINT ["python"]</td></tr>
<tr><td>ENV</td><td>Переменная окружения</td><td>ENV NODE_ENV=production</td></tr>
<tr><td>ARG</td><td>Аргумент сборки</td><td>ARG VERSION=1.0</td></tr>
<tr><td>VOLUME</td><td>Точка монтирования</td><td>VOLUME /data</td></tr>
<tr><td>USER</td><td>Пользователь</td><td>USER node</td></tr>
<tr><td>HEALTHCHECK</td><td>Проверка здоровья</td><td>HEALTHCHECK CMD curl -f http://localhost/</td></tr>
</table>
<h3>Multi-stage builds</h3>
<p>Разделяют этап сборки и финальный образ для уменьшения размера:</p>
<pre><code># Этап сборки
FROM node:18-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Финальный образ
FROM node:18-alpine
WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
EXPOSE 3000
CMD ["node", "dist/server.js"]</code></pre>
<p>Пример для Go:</p>
<pre><code>FROM golang:1.21-alpine AS builder
WORKDIR /src
COPY go.mod go.sum ./
RUN go mod download
COPY . .
RUN CGO_ENABLED=0 go build -o /app

FROM scratch
COPY --from=builder /app /app
ENTRYPOINT ["/app"]</code></pre>
<h3>.dockerignore</h3>
<pre><code>node_modules
.git
.env
*.md
dist
coverage</code></pre>
<h3>Оптимизация и Best Practices</h3>
<ul>
<li>Ставьте часто меняющиеся инструкции позже</li>
<li>Группируйте связанные RUN-команды</li>
<li>Копируйте package файлы отдельно от кода</li>
<li>Используйте конкретные теги образов, не latest</li>
<li>Не запускайте от root — используйте USER</li>
<li>Используйте Alpine-образы для уменьшения размера</li>
<li>Используйте multi-stage builds</li>
<li>Используйте .dockerignore</li>
</ul>'],

            ['title' => 'Docker Compose', 'type' => 'video', 'module' => 'Docker Compose', 'difficulty' => 'medium', 'duration_minutes' => 28,
             'description' => 'docker-compose.yml, сервисы, сети, тома, зависимости, команды.',
             'content' => '<h2>Docker Compose</h2>
<h3>Что такое Docker Compose?</h3>
<p>Docker Compose — инструмент для определения и запуска многоконтейнерных приложений через один YAML-файл.</p>
<h3>Структура docker-compose.yml</h3>
<pre><code>version: "3.8"

services:
  web:
    build: ./web
    ports:
      - "3000:3000"
    environment:
      - DATABASE_URL=postgres://user:pass@db:5432/mydb
    depends_on:
      - db
    restart: unless-stopped

  db:
    image: postgres:15-alpine
    volumes:
      - pgdata:/var/lib/postgresql/data
    environment:
      - POSTGRES_USER=user
      - POSTGRES_PASSWORD=pass
      - POSTGRES_DB=mydb

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

volumes:
  pgdata:</code></pre>
<h3>Конфигурация сервисов</h3>
<ul>
<li><code>build</code> — путь к Dockerfile</li>
<li><code>image</code> — образ из реестра</li>
<li><code>ports</code> — маппинг портов</li>
<li><code>volumes</code> — монтирование томов</li>
<li><code>environment</code> — переменные окружения</li>
<li><code>depends_on</code> — зависимости</li>
<li><code>restart</code> — политика перезапуска</li>
</ul>
<h3>Пользовательские сети</h3>
<pre><code>networks:
  frontend:
    driver: bridge
  backend:
    driver: bridge

services:
  web:
    networks:
      - frontend
  api:
    networks:
      - frontend
      - backend
  db:
    networks:
      - backend</code></pre>
<h3>Healthchecks</h3>
<pre><code>services:
  web:
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:3000/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s</code></pre>
<h3>Файлы override</h3>
<pre><code># docker-compose.override.yml (автоматически)
services:
  web:
    volumes:
      - ./src:/app/src
    environment:
      - NODE_ENV=development

# docker-compose.prod.yml
services:
  web:
    environment:
      - NODE_ENV=production
    restart: always</code></pre>
<pre><code>docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d</code></pre>
<h3>Основные команды</h3>
<table>
<tr><th>Команда</th><th>Описание</th></tr>
<tr><td>docker-compose up -d</td><td>Запуск сервисов</td></tr>
<tr><td>docker-compose down</td><td>Остановка и удаление</td></tr>
<tr><td>docker-compose exec web bash</td><td>Shell в сервисе</td></tr>
<tr><td>docker-compose logs -f</td><td>Просмотр логов</td></tr>
<tr><td>docker-compose build</td><td>Пересборка образов</td></tr>
<tr><td>docker-compose ps</td><td>Список сервисов</td></tr>
<tr><td>docker-compose restart</td><td>Перезапуск</td></tr>
</table>'],

            ['title' => 'Docker networking и volumes', 'type' => 'article', 'module' => 'Docker продвинутый', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Сетевые драйверы, DNS-резолвинг, типы томов, лимиты ресурсов и безопасность.',
             'content' => '<h2>Docker networking и volumes</h2>
<h3>Сетевые драйверы</h3>
<table>
<tr><th>Драйвер</th><th>Описание</th><th>Использование</th></tr>
<tr><td>bridge</td><td>Сеть по умолчанию</td><td>Контейнеры на одном хосте</td></tr>
<tr><td>host</td><td>Удаление сетевой изоляции</td><td>Высокопроизводительные приложения</td></tr>
<tr><td>overlay</td><td>Сеть между хостами</td><td>Docker Swarm, Kubernetes</td></tr>
<tr><td>macvlan</td><td>MAC-адрес контейнеру</td><td>Легаси-приложения</td></tr>
<tr><td>none</td><td>Без сети</td><td>Полная изоляция</td></tr>
</table>
<h3>Управление сетями</h3>
<pre><code>docker network create --driver bridge my-network
docker network ls
docker network inspect my-network
docker run -d --name app --network my-network my-image
docker network disconnect my-network app
docker network rm my-network</code></pre>
<h3>DNS-резолвинг</h3>
<p>В пользовательских сетях контейнеры общаются по DNS-именам (именам сервисов):</p>
<pre><code>services:
  app:
    networks:
      - backend
  db:
    networks:
      - backend
# Внутри app: postgres://db:5432/mydb</code></pre>
<h3>Публикация портов</h3>
<pre><code>docker run -p 8080:80 nginx              # конкретный порт
docker run -P nginx                       # случайный порт
docker run -p 127.0.0.1:8080:80 nginx    # конкретный IP
docker run -p 53:53/udp dns-server       # UDP</code></pre>
<h3>Типы томов</h3>
<p><strong>Named volumes</strong> — управляемый Docker:</p>
<pre><code>docker volume create my-data
docker run -v my-data:/app/data my-image</code></pre>
<p><strong>Bind mounts</strong> — ссылка на директорию хоста:</p>
<pre><code>docker run -v /host/path:/container/path my-image
docker run --mount type=bind,source=/host/path,target=/app/data my-image</code></pre>
<p><strong>tmpfs mounts</strong> — в памяти:</p>
<pre><code>docker run --tmpfs /app/temp my-image</code></pre>
<h3>Резервное копирование</h3>
<pre><code># Бэкап
docker run --rm -v my-data:/source -v $(pwd):/backup alpine \
  tar czf /backup/my-data-backup.tar.gz -C /source .

# Восстановление
docker run --rm -v my-data:/target -v $(pwd):/backup alpine \
  tar xzf /backup/my-data-backup.tar.gz -C /target</code></pre>
<h3>Лимиты ресурсов</h3>
<pre><code># Память и CPU
docker run -m 512m --memory-swap 1g my-image
docker run --cpus="1.5" my-image

# В Docker Compose
services:
  app:
    deploy:
      resources:
        limits:
          cpus: "2.0"
          memory: 1G
        reservations:
          cpus: "0.5"
          memory: 256M</code></pre>
<h3>Очистка ресурсов</h3>
<pre><code>docker container prune
docker image prune -a
docker volume prune
docker system prune -a --volumes</code></pre>
<h3>Безопасность</h3>
<ol>
<li>Не запускайте от root</li>
<li>Используйте read-only ФС: <code>--read-only</code></li>
<li>Ограничивайте capabilities: <code>--cap-drop ALL --cap-add NET_BIND_SERVICE</code></li>
<li>Сканируйте образы: <code>docker scout cves my-image</code></li>
<li>Используйте Docker Content Trust</li>
<li>Не храните секреты в образах</li>
</ol>'],

            ['title' => 'Тест по Docker', 'type' => 'quiz', 'module' => 'Docker тест', 'difficulty' => 'hard', 'duration_minutes' => 15,
             'description' => 'Итоговый тест по Docker.',
             'content' => '<h2>Тест по Docker</h2>'],
        ],
    ],
];
