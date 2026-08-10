<?php

return [
    6 => [ // PostgreSQL
        'lessons' => [
            ['title' => 'Введение в PostgreSQL', 'type' => 'video', 'module' => 'Основы PostgreSQL', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Установка, psql CLI, основы синтаксиса, отличия от MySQL.',
             'content' => '<h2>PostgreSQL</h2>
<pre><code>-- Подключение
psql -U username -d database -h localhost

-- Основные команды psql
\l           -- список БД
\c dbname    -- подключиться к БД
\dt          -- список таблиц
\d table     -- описание таблицы
\q           -- выход

-- Создание таблицы
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    age INT CHECK (age >= 0),
    created_at TIMESTAMPTZ DEFAULT NOW()
);</code></pre>
<h2>Отличия от MySQL</h2>
<ul>
<li>SERIAL vs AUTO_INCREMENT</li>
<li>ILIKE для регистронезависимого поиска</li>
<li>ARRAY типы данных</li>
<li>JSONB вместо JSON</li>
<li>Оконные функции из коробки</li>
</ul>'],
            ['title' => 'SELECT, JOIN, подзапросы', 'type' => 'video', 'module' => 'Расширенные запросы', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'SELECT, JOIN, GROUP BY, HAVING, подзапросы, CTE.',
             'content' => '<h2>Запросы PostgreSQL</h2>
<pre><code>-- CTE (Common Table Expressions)
WITH active_users AS (
    SELECT id, name FROM users WHERE is_active = true
)
SELECT u.name, COUNT(p.id) as posts
FROM active_users u
LEFT JOIN posts p ON u.id = p.user_id
GROUP BY u.id, u.name
HAVING COUNT(p.id) > 0
ORDER BY posts DESC;

-- Коррелированный подзапрос
SELECT u.name, (
    SELECT COUNT(*) FROM posts WHERE user_id = u.id
) as post_count
FROM users u;

-- Интервью запросы
SELECT * FROM events
WHERE created_at >= NOW() - INTERVAL "7 days";

-- Топ-N в группах
SELECT * FROM (
    SELECT *, ROW_NUMBER() OVER (PARTITION BY category_id ORDER BY rating DESC) as rn
    FROM products
) ranked WHERE rn <= 3;</code></pre>'],
            ['title' => 'JSONB: хранение и запросы', 'type' => 'article', 'module' => 'JSONB', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Тип JSONB, операторы, индексы GIN, вложенные данные.',
             'content' => '<h2>JSONB в PostgreSQL</h2>
<pre><code>-- Создание таблицы с JSONB
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    data JSONB NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO events (data) VALUES
(\'{"type": "click", "user": {"id": 1, "name": "Али"}, "page": "/home"}\');

-- Извлечение
SELECT data->>"type" FROM events;                    -- текст
SELECT data->"user"->>"name" FROM events;            -- вложенный
SELECT data#>>"{user,name}" FROM events;              -- путь

-- Операторы
SELECT * FROM events WHERE data @> \'{"type": "click"}\';
SELECT * FROM events WHERE data ? "user";
SELECT * FROM events WHERE data->>"type" = "click";

-- Индекс GIN
CREATE INDEX idx_events_data ON events USING GIN(data);
CREATE INDEX idx_events_type ON events USING GIN((data->"type"));</code></pre>'],
            ['title' => 'Оконные функции и CTE', 'type' => 'video', 'module' => 'Оконные функции', 'difficulty' => 'hard', 'duration_minutes' => 45,
             'description' => 'ROW_NUMBER, RANK, DENSE_RANK, LAG, LEAD, PARTITION BY.',
             'content' => '<h2>Оконные функции</h2>
<pre><code>-- Ранжирование
SELECT name, salary,
    ROW_NUMBER() OVER (ORDER BY salary DESC) as row_num,
    RANK() OVER (ORDER BY salary DESC) as rank_num,
    DENSE_RANK() OVER (ORDER BY salary DESC) as dense_rank
FROM employees;

-- По группам
SELECT name, department, salary,
    ROW_NUMBER() OVER (PARTITION BY department ORDER BY salary DESC) as dept_rank
FROM employees;

-- LAG / LEAD
SELECT month, revenue,
    LAG(revenue, 1) OVER (ORDER BY month) as prev_month,
    LEAD(revenue, 1) OVER (ORDER BY month) as next_month,
    revenue - LAG(revenue, 1) OVER (ORDER BY month) as growth
FROM monthly_sales;

-- Скользящее среднее
SELECT date, amount,
    AVG(amount) OVER (ORDER BY date ROWS BETWEEN 6 PRECEDING AND CURRENT ROW) as avg_7days
FROM daily_sales;</code></pre>'],
            ['title' => 'Индексы и оптимизация', 'type' => 'video', 'module' => 'Оптимизация', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'EXPLAIN ANALYZE, типы индексов, оптимизация запросов.',
             'content' => '<h2>EXPLAIN ANALYZE</h2>
<pre><code>EXPLAIN ANALYZE
SELECT u.name, COUNT(p.id) as posts
FROM users u
JOIN posts p ON u.id = p.user_id
GROUP BY u.id;</code></pre>
<h2>Типы индексов</h2>
<pre><code>-- B-tree (по умолчанию)
CREATE INDEX idx_users_email ON users(email);

-- GIN (для JSONB, массивов, полнотекстового поиска)
CREATE INDEX idx_data ON events USING GIN(data);

-- GiST (геоданные, диапазоны)
CREATE INDEX idx_location ON places USING GiST(location);

-- Partial (частичный)
CREATE INDEX idx_active ON users(email) WHERE is_active = true;

-- Покрывающий индекс
CREATE INDEX idx_covering ON users(name, email) INCLUDE (age);</code></pre>'],
        ],
    ],

    7 => [ // C++
        'lessons' => [
            ['title' => 'Основы C++', 'type' => 'video', 'module' => 'Основы C++', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Переменные, типы данных, операторы, ввод/вывод, условия.',
             'content' => '<h2>Основы C++</h2>
<pre><code>#include &lt;iostream&gt;
using namespace std;

int main() {
    // Переменные
    int age = 25;
    double pi = 3.14159;
    string name = "Али";
    bool active = true;

    // Ввод/вывод
    cout &lt;&lt; "Имя: " &lt;&lt; name &lt;&lt; endl;
    cout &lt;&lt; "Введите возраст: ";
    cin &gt;&gt; age;

    // Условия
    if (age &gt;= 18) {
        cout &lt;&lt; "Совершеннолетний" &lt;&lt; endl;
    } else if (age &gt;= 0) {
        cout &lt;&lt; "Несовершеннолетний" &lt;&lt; endl;
    } else {
        cout &lt;&lt; "Ошибка" &lt;&lt; endl;
    }

    // Циклы
    for (int i = 0; i &lt; 10; i++) {
        cout &lt;&lt; i &lt;&lt; " ";
    }
    cout &lt;&lt; endl;

    int n = 10;
    while (n-- &gt; 0) {
        cout &lt;&lt; n &lt;&lt; " ";
    }

    return 0;
}</code></pre>'],
            ['title' => 'Функции и ссылки', 'type' => 'article', 'module' => 'Основы C++', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Прототипы функций, передача по значению/ссылке, перегрузка.',
             'content' => '<h2>Функции в C++</h2>
<pre><code>#include &lt;iostream&gt;
using namespace std;

// Прототип
int add(int a, int b);
void swap(int &amp;a, int &amp;b);  // по ссылке
const string &amp;maxStr(const string &amp;a, const string &amp;b);  // по конст ссылке

int add(int a, int b) {
    return a + b;
}

void swap(int &amp;a, int &amp;b) {
    int temp = a;
    a = b;
    b = temp;
}

// Перегрузка
int add(int a, int b) { return a + b; }
double add(double a, double b) { return a + b; }

int main() {
    int x = 5, y = 10;
    swap(x, y);  // x=10, y=5
    cout &lt;&lt; add(3, 4) &lt;&lt; endl;     // 7
    cout &lt;&lt; add(3.1, 4.2) &lt;&lt; endl;  // 7.3
    return 0;
}</code></pre>'],
            ['title' => 'ООП в C++', 'type' => 'video', 'module' => 'ООП C++', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'Классы, инкапсуляция, наследование, полиморфизм, виртуальные методы.',
             'content' => '<h2>ООП в C++</h2>
<pre><code>#include &lt;iostream&gt;
using namespace std;

class Animal {
protected:
    string name;
public:
    Animal(string n) : name(n) {}
    virtual void speak() const = 0;  // чисто виртуальный
    virtual ~Animal() {}
};

class Dog : public Animal {
    string breed;
public:
    Dog(string n, string b) : Animal(n), breed(b) {}
    void speak() const override {
        cout &lt;&lt; name &lt;&lt; ": Гав!" &lt;&lt; endl;
    }
};

class Cat : public Animal {
public:
    Cat(string n) : Animal(n) {}
    void speak() const override {
        cout &lt;&lt; name &lt;&lt; ": Мяу!" &lt;&lt; endl;
    }
};

int main() {
    Animal* animals[] = { new Dog("Шарик", "Дворняга"), new Cat("Мурка") };
    for (const auto* a : animals) {
        a-&gt;speak();  // полиморфизм
    }
    for (auto* a : animals) delete a;
    return 0;
}</code></pre>'],
            ['title' => 'Управление памятью', 'type' => 'video', 'module' => 'Управление памятью', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Стек vs куча, new/delete, умные указатели, RAII.',
             'content' => '<h2>Управление памятью</h2>
<pre><code>#include &lt;iostream&gt;
#include &lt;memory&gt;
using namespace std;

// Стек
int x = 10;  // автоматически

// Куча
int* p = new int(42);
delete p;

// Массив в куче
int* arr = new int[100];
delete[] arr;

// Умные указатели (C++11)
unique_ptr&lt;int&gt; up = make_unique&lt;int&gt;(42);
shared_ptr&lt;int&gt; sp = make_shared&lt;int&gt;(42);
weak_ptr&lt;int&gt; wp = sp;

// RAII пример
class FileHandler {
    FILE* file;
public:
    FileHandler(const char* name) : file(fopen(name, "r")) {}
    ~FileHandler() { if (file) fclose(file); }
    // нет копирования
    FileHandler(const FileHandler&amp;) = delete;
    FileHandler&amp; operator=(const FileHandler&amp;) = delete;
};</code></pre>'],
            ['title' => 'STL: контейнеры и алгоритмы', 'type' => 'video', 'module' => 'STL', 'difficulty' => 'hard', 'duration_minutes' => 45,
             'description' => 'vector, map, set, unordered_map, итераторы, алгоритмы STL.',
             'content' => '<h2>STL Контейнеры</h2>
<pre><code>#include &lt;vector&gt;
#include &lt;map&gt;
#include &lt;set&gt;
#include &lt;algorithm&gt;
#include &lt;iostream&gt;
using namespace std;

int main() {
    // Vector
    vector&lt;int&gt; v = {5, 3, 1, 4, 2};
    v.push_back(6);
    v.pop_back();
    sort(v.begin(), v.end());

    // Map
    map&lt;string, int&gt; scores;
    scores["Али"] = 95;
    scores["Боб"] = 87;

    // Set
    set&lt;int&gt; unique = {1, 2, 3, 2, 1};  // {1, 2, 3}

    // Unordered map (хэш-таблица)
    unordered_map&lt;string, double&gt; cache;

    // Алгоритмы
    auto it = find(v.begin(), v.end(), 3);
    int count = count_if(v.begin(), v.end(), [](int x){ return x &gt; 2; });
    for_each(v.begin(), v.end(), [](int x){ cout &lt;&lt; x &lt;&lt; " "; });

    // Lambda
    sort(v.begin(), v.end(), [](int a, int b){ return a &gt; b; });

    return 0;
}</code></pre>'],
            ['title' => 'Шаблоны и метапрограммирование', 'type' => 'article', 'module' => 'Шаблоны', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'Шаблоны функций/классов, SFINAE, constexpr, if constexpr.',
             'content' => '<h2>Шаблоны C++</h2>
<pre><code>// Шаблон функции
template&lt;typename T&gt;
T max(T a, T b) {
    return (a &gt; b) ? a : b;
}

// Шаблон класса
template&lt;typename T, int N&gt;
class Array {
    T data[N];
public:
    T&amp; operator[](int i) { return data[i]; }
    constexpr int size() const { return N; }
};

// constexpr
constexpr int factorial(int n) {
    return (n &lt;= 1) ? 1 : n * factorial(n - 1);
}
static_assert(factorial(5) == 120);  // проверка при компиляции

// if constexpr (C++17)
template&lt;typename T&gt;
string type_name() {
    if constexpr (is_integral_v&lt;T&gt;) return "integral";
    else if constexpr (is_floating_point_v&lt;T&gt;) return "float";
    else return "other";
}</code></pre>'],
        ],
    ],

    8 => [ // Python
        'lessons' => [
            ['title' => 'Синтаксис Python', 'type' => 'video', 'module' => 'Синтаксис', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Переменные, типы данных, отступы, строки, f-строки.',
             'content' => '<h2>Основы Python</h2>
<pre><code># Переменные (без объявления типа)
name = "Али"
age = 25
pi = 3.14159
is_active = True
items = [1, 2, 3]
user = {"name": "Али", "age": 25}

# Отступы (4 пробела)
if age &gt;= 18:
    print("Совершеннолетний")
else:
    print("Несовершеннолетний")

# F-строки
print(f"Привет, {name}! Тебе {age} лет.")

# Множественное присваивание
x, y, z = 1, 2, 3
a = b = 0

# Проверка типа
print(type(name))  # &lt;class "str"&gt;</code></pre>'],
            ['title' => 'Строки и collections', 'type' => 'article', 'module' => 'Синтаксис', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Строки, списки, кортежи, словари, множества, comprehensions.',
             'content' => '<h2>Collections в Python</h2>
<pre><code># Списки
fruits = ["яблоко", "банан", "вишня"]
fruits.append("груша")
fruits.remove("банан")
fruits[1:3]  # срезы

# List comprehension
squares = [x**2 for x in range(10)]
evens = [x for x in range(10) if x % 2 == 0]

# Кортежи (неизменяемые)
point = (3, 4)
x, y = point  # распаковка

# Словари
user = {"name": "Али", "age": 25}
user.get("email", "не указан")
for key, value in user.items():
    print(f"{key}: {value}")

# Dict comprehension
squares_dict = {x: x**2 for x in range(5)}

# Множества
unique = {1, 2, 3, 2, 1}  # {1, 2, 3}
unique.add(4)
a = {1, 2, 3}
b = {3, 4, 5}
print(a & b)   # {3}
print(a | b)   # {1, 2, 3, 4, 5}
print(a - b)   # {1, 2}</code></pre>'],
            ['title' => 'Функции и декораторы', 'type' => 'video', 'module' => 'Функции', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'def, *args/**kwargs, замыкания, декораторы, lambda.',
             'content' => '<h2>Функции Python</h2>
<pre><code># Базовая функция
def greet(name, greeting="Привет"):
    return f"{greeting}, {name}!"

# *args и **kwargs
def log(*args, **kwargs):
    for arg in args:
        print(arg)
    for key, value in kwargs.items():
        print(f"{key}={value}")

log("Hello", "World", level="INFO")

# Lambda
add = lambda a, b: a + b
square = lambda x: x ** 2

# Декораторы
import functools

def timer(func):
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        import time
        start = time.time()
        result = func(*args, **kwargs)
        print(f"{func.__name__}: {time.time()-start:.3f}s")
        return result
    return wrapper

@timer
def slow_function():
    import time
    time.sleep(1)

# Generator
def fibonacci():
    a, b = 0, 1
    while True:
        yield a
        a, b = b, a + b</code></pre>'],
            ['title' => 'ООП в Python', 'type' => 'video', 'module' => 'OOP', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'class, __init__, наследование, dataclass, property, staticmethod.',
             'content' => '<h2>ООП в Python</h2>
<pre><code>from dataclasses import dataclass

# Обычный класс
class Animal:
    def __init__(self, name: str):
        self.name = name

    def speak(self) -> str:
        raise NotImplementedError

    def __repr__(self):
        return f"Animal({self.name!r})"

class Dog(Animal):
    def speak(self) -> str:
        return f"{self.name}: Гав!"

# Dataclass
@dataclass
class Point:
    x: float
    y: float

    def distance_to(self, other: "Point") -&gt; float:
        return ((self.x - other.x)**2 + (self.y - other.y)**2) ** 0.5

# Property
class Circle:
    def __init__(self, radius: float):
        self._radius = radius

    @property
    def radius(self) -&gt; float:
        return self._radius

    @property
    def area(self) -&gt; float:
        import math
        return math.pi * self._radius ** 2

    @staticmethod
    def from_diameter(diameter: float):
        return Circle(diameter / 2)</code></pre>'],
            ['title' => 'Модули и пакеты', 'type' => 'article', 'module' => 'Модули', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'import, пакеты, __init__.py, venv, pip, pyproject.toml.',
             'content' => '<h2>Модули Python</h2>
<pre><code># Импорт
import os
from pathlib import Path
from collections import Counter, defaultdict
from typing import Optional, List

# Стандартная библиотека
import json
import datetime
import re
import random
import itertools

# Полезные модули
from functools import lru_cache, partial
from itertools import chain, groupby
from pathlib import Path

@lru_cache(maxsize=128)
def fibonacci(n):
    if n &lt; 2:
        return n
    return fibonacci(n-1) + fibonacci(n-2)

# pip install requests
# pyproject.toml
# [project]
# name = "myproject"
# dependencies = [
#     "requests&gt;=2.28",
#     "fastapi&gt;=0.100",
# ]</code></pre>'],
            ['title' => 'Асинхронное программирование', 'type' => 'video', 'module' => 'Асинхронность', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'asyncio, async/await, aiohttp, параллельные задачи.',
             'content' => '<h2>asyncio в Python</h2>
<pre><code>import asyncio
import aiohttp

async def fetch(session, url):
    async with session.get(url) as response:
        return await response.text()

async def main():
    async with aiohttp.ClientSession() as session:
        # Параллельные запросы
        urls = [
            "https://api.example.com/users",
            "https://api.example.com/posts",
        ]
        tasks = [fetch(session, url) for url in urls]
        results = await asyncio.gather(*tasks)
        for r in results:
            print(r[:100])

# Запуск
asyncio.run(main())

# async генератор
async def async_range(n):
    for i in range(n):
        await asyncio.sleep(0.1)
        yield i</code></pre>'],
            ['title' => 'Работа с файлами и ошибки', 'type' => 'article', 'module' => 'Модули', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'open(), context manager, pathlib, try/except/finally.',
             'content' => '<h2>Файловый ввод/вывод</h2>
<pre><code># Контекстный менеджер
with open("file.txt", "r", encoding="utf-8") as f:
    content = f.read()
    lines = f.readlines()

# Запись
with open("output.txt", "w") as f:
    f.write("Hello\n")

# pathlib (современный)
from pathlib import Path
p = Path("data")
p.mkdir(exist_ok=True)
for file in p.glob("*.txt"):
    print(file.read_text())

# Обработка ошибок
try:
    result = 10 / 0
except ZeroDivisionError as e:
    print(f"Ошибка: {e}")
except Exception as e:
    print(f"Неожиданная ошибка: {e}")
finally:
    print("Выполнится всегда")

# Собственные исключения
class AppError(Exception):
    pass

class ValidationError(AppError):
    def __init__(self, field, message):
        self.field = field
        self.message = message</code></pre>'],
        ],
    ],

    9 => [ // Java
        'lessons' => [
            ['title' => 'Основы Java', 'type' => 'video', 'module' => 'Основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Типы данных, переменные, условия, циклы, массивы.',
             'content' => '<h2>Основы Java</h2>
<pre><code>public class Main {
    public static void main(String[] args) {
        // Типы данных
        int age = 25;
        double pi = 3.14159;
        String name = "Али";
        boolean active = true;

        // Массивы
        int[] numbers = {1, 2, 3, 4, 5};
        String[] names = new String[10];

        // Условия
        if (age &gt;= 18) {
            System.out.println("Совершеннолетний");
        } else {
            System.out.println("Несовершеннолетний");
        }

        // Тернарный
        String status = age &gt;= 18 ? "взрослый" : "ребёнок";

        // Циклы
        for (int i = 0; i &lt; numbers.length; i++) {
            System.out.println(numbers[i]);
        }
        for (int n : numbers) {
            System.out.println(n);
        }
        int i = 0;
        while (i &lt; 5) { i++; }
        do { i--; } while (i &gt; 0);
    }
}</code></pre>'],
            ['title' => 'ООП в Java', 'type' => 'video', 'module' => 'OOP', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Классы, интерфейсы, абстрактные классы, records, enums.',
             'content' => '<h2>ООП в Java</h2>
<pre><code>// Класс
public class User {
    private String name;
    private int age;

    public User(String name, int age) {
        this.name = name;
        this.age = age;
    }

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    @Override
    public String toString() { return "User{name=" + name + "}"; }
}

// Record (Java 16+)
public record Point(int x, int y) {}

// Enum
public enum Status { ACTIVE, INACTIVE, PENDING }

// Интерфейс
public interface Serializable {
    String toJson();
}

// Наследование
public class Admin extends User implements Serializable {
    public Admin(String name, int age) { super(name, age); }
    public String toJson() { return "{...}"; }
}</code></pre>'],
            ['title' => 'Коллекции Java', 'type' => 'video', 'module' => 'Коллекции', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'ArrayList, HashMap, LinkedList, TreeSet, Stream API.',
             'content' => '<h2>Коллекции Java</h2>
<pre><code>import java.util.*;
import java.util.stream.*;

public class Collections {
    public static void main(String[] args) {
        // ArrayList
        List&lt;String&gt; list = new ArrayList&lt;&gt;();
        list.add("Али");
        list.add("Боб");
        list.get(0);

        // HashMap
        Map&lt;String, Integer&gt; map = new HashMap&lt;&gt;();
        map.put("Али", 95);
        map.get("Али");

        // Stream API
        List&lt;User&gt; users = List.of(
            new User("Али", 25),
            new User("Боб", 30)
        );

        List&lt;String&gt; names = users.stream()
            .filter(u -&gt; u.getAge() &gt; 18)
            .map(User::getName)
            .sorted()
            .collect(Collectors.toList());

        int sum = users.stream()
            .mapToInt(User::getAge)
            .sum();
    }
}</code></pre>'],
            ['title' => 'Многопоточность', 'type' => 'video', 'module' => 'Многопоточность', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'Thread, Runnable, ExecutorService, synchronized, CompletableFuture.',
             'content' => '<h2>Многопоточность Java</h2>
<pre><code>// Thread
Thread t = new Thread(() -&gt; {
    System.out.println("Поток: " + Thread.currentThread().getName());
});
t.start();
t.join();

// ExecutorService
ExecutorService pool = Executors.newFixedThreadPool(4);
List&lt;Future&lt;String&gt;&gt; futures = new ArrayList&lt;&gt;();
for (int i = 0; i &lt; 10; i++) {
    futures.add(pool.submit(() -&gt; process(i)));
}
pool.shutdown();

// synchronized
class Counter {
    private int count = 0;
    public synchronized void increment() { count++; }
}

// CompletableFuture
CompletableFuture.supplyAsync(() -&gt; fetchData())
    .thenApply(data -&gt; parse(data))
    .thenAccept(result -&gt; save(result))
    .exceptionally(e -&gt; { e.printStackTrace(); return null; });</code></pre>'],
            ['title' => 'Spring Boot', 'type' => 'video', 'module' => 'Spring Boot', 'difficulty' => 'hard', 'duration_minutes' => 45,
             'description' => 'REST API, DI, JPA, валидация, тестирование.',
             'content' => '<h2>Spring Boot</h2>
<pre><code>// Controller
@RestController
@RequestMapping("/api/users")
public class UserController {
    @Autowired private UserService service;

    @GetMapping
    public List&lt;UserDto&gt; getAll() {
        return service.findAll();
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public UserDto create(@Valid @RequestBody CreateUserRequest req) {
        return service.create(req);
    }

    @GetMapping("/{id}")
    public UserDto getById(@PathVariable Long id) {
        return service.findById(id);
    }
}

// Entity
@Entity
@Table(name = "users")
public class User {
    @Id @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false)
    private String name;

    @Email @Column(nullable = false, unique = true)
    private String email;
}

// Repository
public interface UserRepository extends JpaRepository&lt;User, Long&gt; {
    List&lt;User&gt; findByNameContaining(String name);
}</code></pre>'],
        ],
    ],

    10 => [ // C#
        'lessons' => [
            ['title' => 'Основы C#', 'type' => 'video', 'module' => 'Основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Переменные, типы данных, условия, циклы, строки.',
             'content' => '<h2>Основы C#</h2>
<pre><code>using System;

class Program {
    static void Main() {
        // Переменные
        int age = 25;
        double pi = 3.14159;
        string name = "Али";
        bool active = true;

        // Вывод
        Console.WriteLine($"Привет, {name}! Тебе {age} лет.");

        // Массивы
        int[] numbers = {1, 2, 3, 4, 5};

        // Условия
        if (age &gt;= 18) Console.WriteLine("Совершеннолетний");
        else Console.WriteLine("Несовершеннолетний");

        // Циклы
        for (int i = 0; i &lt; numbers.Length; i++)
            Console.WriteLine(numbers[i]);

        foreach (var n in numbers)
            Console.WriteLine(n);
    }
}</code></pre>'],
            ['title' => 'ООП и LINQ', 'type' => 'video', 'module' => 'OOP', 'difficulty' => 'medium', 'duration_minutes' => 35,
             'description' => 'Классы, record, интерфейсы, LINQ-запросы.',
             'content' => '<h2>ООП в C#</h2>
<pre><code>// Record (C# 9+)
public record User(string Name, int Age);

// Класс
public class UserService {
    private readonly List&lt;User&gt; _users = new();

    public void Add(User user) =&gt; _users.Add(user);

    public User? Find(string name) =&gt;
        _users.FirstOrDefault(u =&gt; u.Name == name);
}

// Interface
public interface IRepository&lt;T&gt; {
    Task&lt;T?&gt; GetByIdAsync(int id);
    Task&lt;IEnumerable&lt;T&gt;&gt; GetAllAsync();
    Task AddAsync(T entity);
}</code></pre>
<h2>LINQ</h2>
<pre><code>var adults = users
    .Where(u =&gt; u.Age &gt;= 18)
    .OrderBy(u =&gt; u.Name)
    .Select(u =&gt; new { u.Name, u.Age })
    .ToList();

var grouped = users
    .GroupBy(u =&gt; u.Age / 10)
    .Select(g =&gt; new { Decade = g.Key, Count = g.Count() });</code></pre>'],
            ['title' => 'Async/Await в C#', 'type' => 'video', 'module' => 'Async/Await', 'difficulty' => 'medium', 'duration_minutes' => 40,
             'description' => 'async/await, Task, CancellationToken, Exception Handling.',
             'content' => '<h2>Async/Await в C#</h2>
<pre><code>public class DataService {
    private readonly HttpClient _http = new();

    public async Task&lt;string&gt; GetDataAsync(string url) {
        var response = await _http.GetAsync(url);
        response.EnsureSuccessStatusCode();
        return await response.Content.ReadAsStringAsync();
    }

    public async Task&lt;List&lt;UserDto&gt;&gt; GetUsersAsync() {
        var json = await GetDataAsync("https://api.example.com/users");
        return JsonSerializer.Deserialize&lt;List&lt;UserDto&gt;&gt;(json) ?? new();
    }

    // Параллельные запросы
    public async Task&lt;(Users, Posts)&gt; GetAllAsync() {
        var usersTask = GetUsersAsync();
        var postsTask = GetPostsAsync();
        await Task.WhenAll(usersTask, postsTask);
        return (usersTask.Result, postsTask.Result);
    }
}</code></pre>'],
            ['title' => 'Entity Framework Core', 'type' => 'video', 'module' => 'Entity Framework', 'difficulty' => 'hard', 'duration_minutes' => 40,
             'description' => 'DbContext, миграции, запросы, Includes, асинхронные операции.',
             'content' => '<h2>Entity Framework Core</h2>
<pre><code>// DbContext
public class AppDbContext : DbContext {
    public DbSet&lt;User&gt; Users =&gt; Set&lt;User&gt;();

    protected override void OnConfiguring(DbContextOptionsBuilder options) {
        options.UseNpgsql("Host=localhost;Database=mydb");
    }
}

// Миграции
dotnet ef migrations add InitialCreate
dotnet ef database update

// Запросы
var users = await context.Users
    .Where(u =&gt; u.Age &gt; 18)
    .OrderBy(u =&gt; u.Name)
    .ToListAsync();

var user = await context.Users
    .Include(u =&gt; u.Posts)
    .FirstOrDefaultAsync(u =&gt; u.Id == id);

// CRUD
context.Users.Add(new User { Name = "Али", Email = "ali@mail.com" });
await context.SaveChangesAsync();</code></pre>'],
            ['title' => 'ASP.NET Core MVC', 'type' => 'video', 'module' => 'ASP.NET Core', 'difficulty' => 'hard', 'duration_minutes' => 45,
             'description' => 'Controllers, Views, DI, Middleware, конфигурация.',
             'content' => '<h2>ASP.NET Core</h2>
<pre><code>// Program.cs
var builder = WebApplication.CreateBuilder(args);
builder.Services.AddControllersWithViews();
builder.Services.AddScoped&lt;IUserService, UserService&gt;();

var app = builder.Build();
app.MapControllerRoute("default", "{controller=Home}/{action=Index}/{id?}");
app.Run();

// Controller
public class HomeController : Controller {
    private readonly IUserService _userService;

    public HomeController(IUserService userService) {
        _userService = userService;
    }

    public async Task&lt;IActionResult&gt; Index() {
        var users = await _userService.GetAllAsync();
        return View(users);
    }

    [HttpPost]
    public async Task&lt;IActionResult&gt; Create(CreateUserRequest req) {
        if (!ModelState.IsValid) return View(req);
        await _userService.CreateAsync(req);
        return RedirectToAction(nameof(Index));
    }
}</code></pre>'],
        ],
    ],
];
