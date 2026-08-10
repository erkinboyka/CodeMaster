<?php

return [
    18 => [ // Kubernetes
        'lessons' => [
            ['title' => 'Архитектура Kubernetes', 'type' => 'video', 'module' => 'Kubernetes архитектура', 'difficulty' => 'easy', 'duration_minutes' => 30,
             'description' => 'Master/worker компоненты, API server, etcd, kubelet, kubectl, настройка кластера.',
             'content' => '<h2>Что такое Kubernetes</h2>
<p>Kubernetes (K8s) — это open-source система оркестрации контейнеров, которая автоматизирует развертывание, масштабирование и управление контейнеризированными приложениями. Создана Google, сейчас поддерживается CNCF.</p>

<h3>Компоненты Master-ноды</h3>
<ul>
<li><strong>API Server (kube-apiserver)</strong> — точка входа для всех REST-запросов. Валидирует и обрабатывает команды kubectl, передаёт состояние в etcd.</li>
<li><strong>etcd</strong> — распределённое хранилище ключ-значение. Хранит все данные кластера: конфигурации, состояния Pod-ов, секреты.</li>
<li><strong>Scheduler (kube-scheduler)</strong> — назначает Pod-ы на Worker-ноды на основе ресурсов, affinity/anti-affinity, taints и толерансов.</li>
<li><strong>Controller Manager (kube-controller-manager)</strong> — управляет текущим состоянием кластера: ReplicaSet, Deployment, Node, Endpoint контроллеры.</li>
</ul>

<h3>Компоненты Worker-ноды</h3>
<ul>
<li><strong>kubelet</strong> — агент на каждой ноде, следит за Pod-ами, докладывает о состоянии в API Server.</li>
<li><strong>kube-proxy</strong> — сетевой прокси, реализует Service через iptables или IPVS правила.</li>
<li><strong>Container Runtime</strong> — движок запуска контейнеров: containerd, CRI-O, Docker (deprecated).</li>
</ul>

<h3>Основные команды kubectl</h3>
<pre><code>kubectl get pods                          # список Pod-ов
kubectl get nodes                        # список нод
kubectl describe pod &lt;name&gt;             # детали Pod-а
kubectl apply -f manifest.yaml           # применить манифест
kubectl delete -f manifest.yaml          # удалить ресурс
kubectl logs &lt;pod-name&gt;                 # логи Pod-а
kubectl exec -it &lt;pod-name&gt; -- bash    # shell в Pod-е
kubectl port-forward &lt;pod&gt; 8080:80      # проброс порта
kubectl scale deployment app --replicas=5 # масштабирование</code></pre>

<h3>Настройка кластера</h3>
<table>
<tr><th>Инструмент</th><th>Описание</th><th>Использование</th></tr>
<tr><td>Minikube</td><td>Локальный кластер в VM</td><td>Разработка и тестирование</td></tr>
<tr><td>kubeadm</td><td>Утилита для production</td><td>Настройка реальных кластеров</td></tr>
<tr><td>k3s</td><td>Легковесный K8s</td><td>Edge-устройства, IoT</td></tr>
<tr><td>EKS/AKS/GKE</td><td>Облачные сервисы</td><td>Managed Kubernetes</td></tr>
</table>'],

            ['title' => 'Pods, ReplicaSets и Deployments', 'type' => 'video', 'module' => 'Kubernetes Pods', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'YAML манифесты, Pods, multi-container, init containers, probes, rolling updates.',
             'content' => '<h2>Pod — базовая единица развертывания</h2>
<pre><code>apiVersion: v1
kind: Pod
metadata:
  name: my-app
  labels:
    app: my-app
    env: production
spec:
  containers:
  - name: nginx
    image: nginx:1.25-alpine
    ports:
    - containerPort: 80
    resources:
      requests:
        cpu: "100m"
        memory: "128Mi"
      limits:
        cpu: "500m"
        memory: "256Mi"
    env:
    - name: DATABASE_URL
      valueFrom:
        secretKeyRef:
          name: db-secret
          key: url</code></pre>

<h3>Multi-container Pods</h3>
<pre><code>spec:
  containers:
  - name: app
    image: myapp:1.0
  - name: sidecar
    image: log-agent:latest
  - name: ambassador
    image: envoyproxy:latest</code></pre>

<h3>Init Containers</h3>
<pre><code>spec:
  initContainers:
  - name: init-db
    image: busybox
    command: ["sh", "-c", "until nslookup postgres; do sleep 2; done"]
  containers:
  - name: app
    image: myapp:1.0</code></pre>

<h3>Liveness и Readiness Probes</h3>
<pre><code>livenessProbe:
  httpGet:
    path: /healthz
    port: 8080
  initialDelaySeconds: 15
  periodSeconds: 10

readinessProbe:
  httpGet:
    path: /ready
    port: 8080
  initialDelaySeconds: 5
  periodSeconds: 5</code></pre>

<h3>Deployment</h3>
<pre><code>apiVersion: apps/v1
kind: Deployment
metadata:
  name: web-app
spec:
  replicas: 3
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  selector:
    matchLabels:
      app: web-app
  template:
    metadata:
      labels:
        app: web-app
    spec:
      containers:
      - name: web
        image: nginx:1.25</code></pre>

<h3>Команды управления</h3>
<pre><code>kubectl rollout status deployment/web-app     # статус
kubectl rollout history deployment/web-app    # история
kubectl rollout undo deployment/web-app       # откат
kubectl rollout undo --to-revision=2          # откат к версии
kubectl scale deployment web-app --replicas=5 # масштаб</code></pre>'],

            ['title' => 'Services, Ingress, ConfigMaps и Secrets', 'type' => 'video', 'module' => 'Kubernetes сети', 'difficulty' => 'medium', 'duration_minutes' => 32,
             'description' => 'ClusterIP, NodePort, LoadBalancer, Ingress, ConfigMaps, Secrets.',
             'content' => '<h2>Service — стабильная сеть для Pod-ов</h2>

<h3>ClusterIP (по умолчанию)</h3>
<pre><code>apiVersion: v1
kind: Service
metadata:
  name: backend-service
spec:
  type: ClusterIP
  selector:
    app: backend
  ports:
  - port: 80
    targetPort: 8080</code></pre>

<h3>NodePort</h3>
<pre><code>apiVersion: v1
kind: Service
metadata:
  name: frontend-service
spec:
  type: NodePort
  selector:
    app: frontend
  ports:
  - port: 80
    targetPort: 3000
    nodePort: 30080</code></pre>

<h3>LoadBalancer</h3>
<pre><code>apiVersion: v1
kind: Service
metadata:
  name: api-gateway
  annotations:
    service.beta.kubernetes.io/aws-load-balancer-type: nlb
spec:
  type: LoadBalancer
  selector:
    app: api
  ports:
  - port: 443
    targetPort: 8443</code></pre>

<h3>ExternalName</h3>
<pre><code>apiVersion: v1
kind: Service
metadata:
  name: external-db
spec:
  type: ExternalName
  externalName: db.example.com</code></pre>

<h3>Ingress</h3>
<pre><code>apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: app-ingress
  annotations:
    nginx.ingress.kubernetes.io/rewrite-target: /
spec:
  tls:
  - hosts:
    - app.example.com
    secretName: app-tls
  rules:
  - host: app.example.com
    http:
      paths:
      - path: /api
        pathType: Prefix
        backend:
          service:
            name: api-service
            port:
              number: 80
      - path: /
        pathType: Prefix
        backend:
          service:
            name: frontend-service
            port:
              number: 80</code></pre>

<h3>ConfigMap и Secrets</h3>
<pre><code># ConfigMap
kubectl create configmap app-config --from-literal=ENV=production
kubectl create configmap app-config --from-file=config.yaml

# Secrets
kubectl create secret generic db-secret --from-literal=password=mysecret
kubectl create secret tls app-tls --cert=tls.crt --key=tls.key</code></pre>

<p>ConfigMaps хранят конфигурационные данные, а Secrets — зашифрованные чувствительные данные (пароли, токены, сертификаты).</p>'],

            ['title' => 'Persistent Volumes, StatefulSets и мониторинг', 'type' => 'video', 'module' => 'Kubernetes хранилище', 'difficulty' => 'hard', 'duration_minutes' => 35,
             'description' => 'PV, PVC, StorageClass, StatefulSets, DaemonSets, Jobs, HPA, Prometheus, Grafana.',
             'content' => '<h2>PersistentVolume и PersistentVolumeClaim</h2>
<pre><code>apiVersion: v1
kind: PersistentVolume
metadata:
  name: nfs-pv
spec:
  capacity:
    storage: 50Gi
  accessModes:
  - ReadWriteMany
  persistentVolumeReclaimPolicy: Retain
  nfs:
    server: 10.0.0.10
    path: /exports/data</code></pre>

<pre><code>apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: data-pvc
spec:
  accessModes:
  - ReadWriteOnce
  resources:
    requests:
      storage: 10Gi
  storageClassName: standard</code></pre>

<h3>StorageClass</h3>
<pre><code>apiVersion: storage.k8s.io/v1
kind: StorageClass
metadata:
  name: fast
provisioner: kubernetes.io/aws-ebs
parameters:
  type: gp3
reclaimPolicy: Delete
allowVolumeExpansion: true</code></pre>

<h3>StatefulSet</h3>
<pre><code>apiVersion: apps/v1
kind: StatefulSet
metadata:
  name: postgres
spec:
  serviceName: postgres
  replicas: 3
  selector:
    matchLabels:
      app: postgres
  template:
    spec:
      containers:
      - name: postgres
        image: postgres:16
        volumeMounts:
        - name: data
          mountPath: /var/lib/postgresql/data
  volumeClaimTemplates:
  - metadata:
      name: data
    spec:
      accessModes: ["ReadWriteOnce"]
      resources:
        requests:
          storage: 20Gi</code></pre>

<h3>DaemonSet, Job, CronJob</h3>
<pre><code>apiVersion: batch/v1
kind: CronJob
metadata:
  name: backup
spec:
  schedule: "0 2 * * *"
  jobTemplate:
    spec:
      template:
        spec:
          containers:
          - name: backup
            image: backup-tool:latest
          restartPolicy: OnFailure</code></pre>

<h3>HorizontalPodAutoscaler</h3>
<pre><code>apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: app-hpa
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: app
  minReplicas: 2
  maxReplicas: 10
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70</code></pre>

<h3>Мониторинг: Prometheus + Grafana</h3>
<ul>
<li><strong>metrics-server</strong> — базовые метрики CPU/内存 для kubectl top</li>
<li><strong>Prometheus</strong> — сбор и хранение метрик, алерты</li>
<li><strong>Grafana</strong> — визуализация дашбордов</li>
<li><strong>kube-state-metrics</strong> — метрики состояния K8s объектов</li>
</ul>
<pre><code>kubectl top nodes                      # ресурсы нод
kubectl top pods                       # ресурсы Pod-ов
helm install prometheus prometheus-community/kube-prometheus-stack</code></pre>'],

            ['title' => 'Тест по Kubernetes', 'type' => 'quiz', 'module' => 'Kubernetes тест', 'difficulty' => 'hard', 'duration_minutes' => 15,
             'description' => 'Итоговый тест по Kubernetes.',
             'content' => '<h2>Тест по Kubernetes</h2>'],
        ],
    ],

    19 => [ // Mobile Development
        'lessons' => [
            ['title' => 'Обзор мобильной разработки', 'type' => 'video', 'module' => 'Mobile обзор', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Нативная vs кроссплатформенная, React Native vs Flutter, среда разработки.',
             'content' => '<h2>Подходы к мобильной разработке</h2>

<h3>Сравнение нативной и кроссплатформенной разработки</h3>
<table>
<tr><th>Критерий</th><th>Нативная</th><th>Кроссплатформенная</th></tr>
<tr><td>Языки</td><td>Swift (iOS), Kotlin (Android)</td><td>JavaScript/Dart</td></tr>
<tr><td>Производительность</td><td>Максимальная</td><td>Хорошая (90-95%)</td></tr>
<tr><td>Доступ к API</td><td>Полный</td><td>Через плагины</td></tr>
<tr><td>Разработка</td><td>2 команды</td><td>1 команда</td></tr>
<tr><td>Обновления</td><td>Через App Store</td><td>OTA обновления</td></tr>
<tr><td>Стоимость</td><td>Высокая</td><td>Средняя</td></tr>
</table>

<h3>Архитектура React Native</h3>
<ul>
<li><strong>JavaScript Thread</strong> — выполнение React-кода и бизнес-логики</li>
<li><strong>Native Thread</strong> — рендеринг нативных виджетов iOS/Android</li>
<li><strong>JS Bridge</strong> — связь между JS и нативным кодом (сериализация JSON)</li>
<li><strong>New Architecture (Fabric)</strong> — прямой вызов C++ через JSI без моста</li>
</ul>

<h3>Архитектура Flutter</h3>
<ul>
<li><strong>Dart</strong> — язык программирования с JIT/AOT компиляцией</li>
<li><strong>Skia Engine</strong> — кастомный рендерер, рисует пиксели напрямую</li>
<li><strong>Impeller</strong> — новый рендерер для iOS (запланирован для Android)</li>
<li><strong>Widget Tree</strong> — всё это виджеты: текст, кнопки, отступы</li>
</ul>

<h3>Среда разработки</h3>
<table>
<tr><th>Инструмент</th><th>React Native</th><th>Flutter</th></tr>
<tr><td>IDE</td><td>VS Code, Android Studio</td><td>VS Code, Android Studio, IntelliJ</td></tr>
<tr><td>iOS</td><td>Xcode + CocoaPods</td><td>Xcode</td></tr>
<tr><td>Android</td><td>Android SDK, JDK</td><td>Android SDK, JDK</td></tr>
<tr><td>Отладка</td><td>Flipper, React DevTools</td><td>Flutter DevTools, Observatory</td></tr>
</table>

<h3>Структура проекта React Native</h3>
<pre><code>MyApp/
├── android/
├── ios/
├── src/
│   ├── components/
│   ├── screens/
│   ├── navigation/
│   └── services/
├── App.tsx
├── package.json
└── tsconfig.json</code></pre>

<h3>Структура проекта Flutter</h3>
<pre><code>my_app/
├── android/
├── ios/
├── lib/
│   ├── main.dart
│   ├── screens/
│   ├── widgets/
│   └── models/
├── test/
├── pubspec.yaml
└── analysis_options.yaml</code></pre>'],

            ['title' => 'React Native: компоненты и навигация', 'type' => 'video', 'module' => 'React Native компоненты', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'View, Text, FlatList, StyleSheet, React Navigation, state management, AsyncStorage.',
             'content' => '<h2>Базовые компоненты React Native</h2>
<pre><code>import React from "react";
import { View, Text, Image, ScrollView, FlatList, StyleSheet } from "react-native";

export default function App() {
  const data = [
    { id: "1", title: "Item 1" },
    { id: "2", title: "Item 2" },
  ];

  return (
    &lt;ScrollView&gt;
      &lt;View style={styles.container}&gt;
        &lt;Text style={styles.title}&gt;Привет!&lt;/Text&gt;
        &lt;Image source={{ uri: "https://example.com/img.png" }} style={styles.img} /&gt;
        &lt;FlatList
          data={data}
          keyExtractor={(item) =&gt; item.id}
          renderItem={({ item }) =&gt; &lt;Text&gt;{item.title}&lt;/Text&gt;}
        /&gt;
      &lt;/View&gt;
    &lt;/ScrollView&gt;
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16 },
  title: { fontSize: 24, fontWeight: "bold" },
  img: { width: 200, height: 200, borderRadius: 8 },
});</code></pre>

<h3>React Navigation</h3>
<pre><code>import { NavigationContainer } from "@react-navigation/native";
import { createNativeStackNavigator } from "@react-navigation/native-stack";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

function AppNavigator() {
  return (
    &lt;NavigationContainer&gt;
      &lt;Stack.Navigator&gt;
        &lt;Stack.Screen name="Home" component={HomeScreen} /&gt;
        &lt;Stack.Screen name="Details" component={DetailsScreen} /&gt;
      &lt;/Stack.Navigator&gt;
    &lt;/NavigationContainer&gt;
  );
}</code></pre>

<h3>State Management: Redux Toolkit</h3>
<pre><code>import { createSlice, configureStore } from "@reduxjs/toolkit";

const counterSlice = createSlice({
  name: "counter",
  initialState: { value: 0 },
  reducers: {
    increment: (state) =&gt; { state.value += 1; },
    decrement: (state) =&gt; { state.value -= 1; },
  },
});

const store = configureStore({ reducer: { counter: counterSlice.reducer } });</code></pre>

<h3>Networking и AsyncStorage</h3>
<pre><code>// fetch
const res = await fetch("https://api.example.com/users");
const data = await res.json();

// AsyncStorage
import AsyncStorage from "@react-native-async-storage/async-storage";
await AsyncStorage.setItem("token", "abc123");
const token = await AsyncStorage.getItem("token");

// Deep Links
const linking = {
  prefixes: ["myapp://"],
  config: { screens: { Home: "home", Profile: "user/:id" } },
};</code></pre>'],

            ['title' => 'Flutter: виджеты и управление состоянием', 'type' => 'video', 'module' => 'Flutter виджеты', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'StatelessWidget, StatefulWidget, Material Design, Provider, Riverpod, навигация.',
             'content' => '<h2>Базовые виджеты Flutter</h2>
<pre><code>import "package:flutter/material.dart";

void main() =&gt; runApp(const MyApp());

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: "Flutter App",
      theme: ThemeData(primarySwatch: Colors.blue),
      home: const HomeScreen(),
    );
  }
}</code></pre>

<h3>Material Design виджеты</h3>
<pre><code>class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Главная")),
      body: ListView(
        children: [
          Card(
            child: ListTile(
              leading: const Icon(Icons.person),
              title: const Text("Профиль"),
              subtitle: const Text("Настройки аккаунта"),
              trailing: const Icon(Icons.chevron_right),
            ),
          ),
          const Padding(
            padding: EdgeInsets.all(16.0),
            child: Text("Добро пожаловать!"),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {},
        child: const Icon(Icons.add),
      ),
    );
  }
}</code></pre>

<h3>StatefulWidget и setState</h3>
<pre><code>class CounterScreen extends StatefulWidget {
  const CounterScreen({super.key});

  @override
  State&lt;CounterScreen&gt; createState() =&gt; _CounterScreenState();
}

class _CounterScreenState extends State&lt;CounterScreen&gt; {
  int _count = 0;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text("Счётчик: $_count"),
        ElevatedButton(
          onPressed: () =&gt; setState(() =&gt; _count++),
          child: const Text("Увеличить"),
        ),
      ],
    );
  }
}</code></pre>

<h3>State Management: Provider</h3>
<pre><code>import "package:provider/provider.dart";

class CounterModel extends ChangeNotifier {
  int _count = 0;
  int get count =&gt; _count;
  void increment() { _count++; notifyListeners(); }
}

void main() {
  runApp(
    ChangeNotifierProvider(
      create: (_) =&gt; CounterModel(),
      child: const MyApp(),
    ),
  );
}

// В виджете
Text(context.watch&lt;CounterModel&gt;().count.toString())</code></pre>

<h3>Навигация</h3>
<pre><code>// Named routes
Navigator.pushNamed(context, "/details", arguments: {"id": 42});

// MaterialPageRoute
Navigator.push(context, MaterialPageRoute(
  builder: (context) =&gt; const DetailsScreen(),
));</code></pre>'],

            ['title' => 'Публикация и мониторинг приложений', 'type' => 'video', 'module' => 'Mobile публикация', 'difficulty' => 'hard', 'duration_minutes' => 30,
             'description' => 'Google Play, App Store, Fastlane CI/CD, Crashlytics, ASO.',
             'content' => '<h2>Google Play Store</h2>
<ol>
<li>Создайте аккаунт разработчика ($25 разово)</li>
<li>Сгенерируйте keystore: <code>keytool -genkey -v -keystore upload.keystore</code></li>
<li>Соберите AAB: <code>flutter build apprelease</code></li>
<li>Заполните Store Listing: описание, скриншоты, иконка</li>
<li>Укажите Content Rating (вопросы-анкета)</li>
<li>Настройте Staged Rollout (10% → 50% → 100%)</li>
<li>Опубликуйте и ждите модерацию (1-3 дня)</li>
</ol>

<h2>Apple App Store</h2>
<ol>
<li>Apple Developer Account ($99/год)</li>
<li>Создайте Certificates и Provisioning Profiles в Apple Developer Portal</li>
<li>Соберите архив: Xcode → Product → Archive</li>
<li>Загрузите в App Store Connect через Transporter</li>
<li>TestFlight для бета-тестирования (до 10,000 тестеров)</li>
<li>Отправьте на ревью Apple (1-7 дней)</li>
</ol>

<h3>App Store Review Guidelines</h3>
<ul>
<li>Нет приватных API без обоснования</li>
<li>Контент соответствует рейтингу</li>
<li>Работоспособность и отсутствие крэшей</li>
<li>Уважение к privacy (App Privacy Labels)</li>
<li>Нет магазинов внутри приложения (commission 15-30%)</li>
</ul>

<h3>CI/CD с Fastlane</h3>
<pre><code># Fastfile
default_platform(:ios)

platform :ios do
  lane :beta do
    build_app(scheme: "MyApp")
    upload_to_testflight
  end

  lane :release do
    build_app(scheme: "MyApp")
    upload_to_app_store(skip_screenshots: true)
  end
end

# Запуск
fastlane ios beta
fastlane ios release</code></pre>

<h3>Crashlytics и аналитика</h3>
<pre><code>// Firebase Crashlytics
FirebaseCrashlytics.instance.recordError(error, stackTrace);
FirebaseCrashlytics.instance.log("User clicked button");

// Analytics
FirebaseAnalytics.instance.logEvent(name: "purchase", parameters: {
  "item_id": "SKU_123",
  "price": 9.99,
});</code></pre>

<h3>ASO (App Store Optimization)</h3>
<ul>
<li>Ключевые слова в заголовке и subtitle (30 символов)</li>
<li>Описание с релевантными словами</li>
<li>Качественные скриншоты и превью видео</li>
<li>Рейтинги и отзывы (промпт после успешного действия)</li>
<li>Локализация для разных рынков</li>
</ul>'],

            ['title' => 'Тест по Mobile Development', 'type' => 'quiz', 'module' => 'Mobile тест', 'difficulty' => 'hard', 'duration_minutes' => 15,
             'description' => 'Итоговый тест по Mobile Development.',
             'content' => '<h2>Тест по Mobile Development</h2>'],
        ],
    ],

    20 => [ // English A1
        'lessons' => [
            ['title' => 'Алфавит и произношение', 'type' => 'video', 'module' => 'English основы', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'Английский алфавит A-Z с IPA, гласные/согласные, произношение, приветствия.',
             'content' => '<h2>Английский алфавит</h2>

<h3>Буквы и транскрипция</h3>
<table>
<tr><th>Буква</th><th>IPA</th><th>Буква</th><th>IPA</th></tr>
<tr><td>A a</td><td>/eɪ/</td><td>N n</td><td>/en/</td></tr>
<tr><td>B b</td><td>/biː/</td><td>O o</td><td>/əʊ/</td></tr>
<tr><td>C c</td><td>/siː/</td><td>P p</td><td>/piː/</td></tr>
<tr><td>D d</td><td>/diː/</td><td>Q q</td><td>/kjuː/</td></tr>
<tr><td>E e</td><td>/iː/</td><td>R r</td><td>/ɑːr/</td></tr>
<tr><td>F f</td><td>/ɛf/</td><td>S s</td><td>/ɛs/</td></tr>
<tr><td>G g</td><td>/dʒiː/</td><td>T t</td><td>/tiː/</td></tr>
<tr><td>H h</td><td>/eɪtʃ/</td><td>U u</td><td>/juː/</td></tr>
<tr><td>I i</td><td>/aɪ/</td><td>V v</td><td>/viː/</td></tr>
<tr><td>J j</td><td>/dʒeɪ/</td><td>W w</td><td>/ˈdʌbljuː/</td></tr>
<tr><td>K k</td><td>/keɪ/</td><td>X x</td><td>/ɛks/</td></tr>
<tr><td>L l</td><td>/ɛl/</td><td>Y y</td><td>/waɪ/</td></tr>
<tr><td>M m</td><td>/ɛm/</td><td>Z z</td><td>/zɛd/</td></tr>
</table>

<h3>Гласные и согласные</h3>
<p><strong>Гласные (5):</strong> A, E, I, O, U — образуют слоги, всегда звучат в слове.</p>
<p><strong>Согласные (21):</strong> B, C, D, F, G, H, J, K, L, M, N, P, Q, R, S, T, V, W, X, Y, Z.</p>

<h3>Правила произношения</h3>
<ul>
<li><strong>Silent letters:</strong> knife (k), know (k), psychology (p), bomb (b)</li>
<li><strong>Ударение:</strong> PREsent (существительное) vs preSENT (глагол)</li>
<li><strong>-ed окончания:</strong> /t/ (watched), /d/ (played), /ɪd/ (wanted)</li>
<li><strong>Th:</strong> voiceless /θ/ (think), voiced /ð/ (this)</li>
</ul>

<h3>Базовые приветствия</h3>
<table>
<tr><th>Фраза</th><th>Перевод</th><th>Когда использовать</th></tr>
<tr><td>Hello!</td><td>Привет!</td><td>Универсальное приветствие</td></tr>
<tr><td>Hi!</td><td>Привет!</td><td>Неформальное</td></tr>
<tr><td>Good morning!</td><td>Доброе утро!</td><td>До 12:00</td></tr>
<tr><td>Good afternoon!</td><td>Добрый день!</td><td>12:00 - 18:00</td></tr>
<tr><td>Good evening!</td><td>Добрый вечер!</td><td>После 18:00</td></tr>
<tr><td>How are you?</td><td>Как дела?</td><td>Универсальный вопрос</td></tr>
<tr><td>Fine, thanks!</td><td>Хорошо, спасибо!</td><td>Стандартный ответ</td></tr>
</table>

<h3>Знакомство и вежливые фразы</h3>
<ul>
<li><strong>My name is...</strong> — Меня зовут...</li>
<li><strong>I am from...</strong> — Я из...</li>
<li><strong>Nice to meet you!</strong> — Приятно познакомиться!</li>
<li><strong>Please</strong> — Пожалуйста (при просьбе)</li>
<li><strong>Thank you</strong> — Спасибо</li>
<li><strong>Excuse me</strong> — Извините (обращение)</li>
<li><strong>Sorry</strong> — Извините (извинение)</li>
<li><strong>You are welcome</strong> — Не за что</li>
</ul>'],

            ['title' => 'Артикли, местоимения и числа', 'type' => 'video', 'module' => 'English грамматика', 'difficulty' => 'easy', 'duration_minutes' => 25,
             'description' => 'a/an/the, личные/притяжательные/объектные местоимения, единственное и множественное число.',
             'content' => '<h2>Артикли в английском языке</h2>

<h3>Неопределённый артикль a/an</h3>
<p>Используется перед исчисляемыми существительными в единственном числе, когда объект новый или неизвестен.</p>
<table>
<tr><th>Правило</th><th>Пример</th><th>Перевод</th></tr>
<tr><td>Перед согласной</td><td>a book, a cat, a university</td><td>книга, кошка, университет</td></tr>
<tr><td>Перед гласной</td><td>an apple, an hour, an umbrella</td><td>яблоко, час, зонтик</td></tr>
<tr><td>Перед прилагательным + существительное</td><td>a beautiful day</td><td>прекрасный день</td></tr>
</table>

<h3>Определённый артикль the</h3>
<p>Используется когда объект конкретен, уже известен собеседнику или единственный в своём роде.</p>
<ul>
<li><strong>the sun, the moon</strong> — единственные (the солнце, луна)</li>
<li><strong>the book on the table</strong> — конкретная книга на столе</li>
<li><strong>the first, the best</strong> — превосходные степени</li>
<li><strong>the United States, the Pacific Ocean</strong> — географические названия</li>
</ul>

<h3>Нулевой артикль (без артикля)</h3>
<ul>
<li><strong>Множественное число:</strong> I like cats (Я люблю кошек)</li>
<li><strong>Неисчисляемые:</strong> Water is important (Вода важна)</li>
<li><strong>Имена собственные:</strong> Moscow, John</li>
<li><strong>Дни, месяцы:</strong> Monday, January</li>
</ul>

<h2>Личные местоимения</h2>
<table>
<tr><th>Подлежащие</th><th>Объектные</th><th>Притяжательные прил.</th><th>Притяжательные мест.</th></tr>
<tr><td>I — я</td><td>me — меня</td><td>my — мой</td><td>mine — мой (тот же)</td></tr>
<tr><td>you — ты/вы</td><td>you — тебя/вас</td><td>your — твой/ваш</td><td>yours — твой</td></tr>
<tr><td>he — он</td><td>him — его</td><td>his — его</td><td>his — его</td></tr>
<tr><td>she — она</td><td>her — её</td><td>her — её</td><td>hers — её</td></tr>
<tr><td>it — оно</td><td>it — его</td><td>its — его</td><td>its — его</td></tr>
<tr><td>we — мы</td><td>us — нас</td><td>our — наш</td><td>ours — наш</td></tr>
<tr><td>they — они</td><td>them — их</td><td>their — их</td><td>theirs — их</td></tr>
</table>

<h3>Указательные местоимения</h3>
<ul>
<li><strong>this</strong> — это (ед.ч., близкий объект): This is my book.</li>
<li><strong>that</strong> — то (ед.ч., далёкий объект): That is your car.</li>
<li><strong>these</strong> — эти (мн.ч., близкий): These are my friends.</li>
<li><strong>those</strong> — те (мн.ч., далёкий): Those are your keys.</li>
</ul>

<h2>Единственное и множественное число</h2>

<h3>Правила образования множественного числа</h3>
<table>
<tr><th>Правило</th><th>Единственное</th><th>Множественное</th></tr>
<tr><td>+s</td><td>book</td><td>books</td></tr>
<tr><td>+es (s, x, sh, ch)</td><td>box, bus, watch</td><td>boxes, buses, watches</td></tr>
<tr><td>consonant + y → ies</td><td>city, baby</td><td>cities, babies</td></tr>
<tr><td>vowel + y → ys</td><td>boy, day</td><td>boys, days</td></tr>
<tr><td>-f / -fe → -ves</td><td>wife, knife</td><td>wives, knives</td></tr>
</table>

<h3>Неправильные формы</h3>
<table>
<tr><th>Единственное</th><th>Множественное</th></tr>
<tr><td>child</td><td>children</td></tr>
<tr><td>man</td><td>men</td></tr>
<tr><td>woman</td><td>women</td></tr>
<tr><td>tooth</td><td>teeth</td></tr>
<tr><td>foot</td><td>feet</td></tr>
<tr><td>mouse</td><td>mice</td></tr>
<tr><td>person</td><td>people</td></tr>
</table>'],

            ['title' => 'Present Simple и Present Continuous', 'type' => 'video', 'module' => 'English времена', 'difficulty' => 'medium', 'duration_minutes' => 30,
             'description' => 'Present Simple/Continuous: образование, сигналы, различия, ошибки.',
             'content' => '<h2>Present Simple — настоящее простое</h2>

<h3>Образование</h3>
<pre><code>Утвердительное:
I work / You work / He works / She works / It works / We work / They work

Отрицательное:
I don\'t work / He doesn\'t work

Вопросительное:
Do you work? / Does he work?

Вопрос с вопросительным словом:
Where do you work? / What does she do?</code></pre>

<h3>Сигнальные слова</h3>
<ul>
<li><strong>always</strong> — всегда: I always wake up at 7.</li>
<li><strong>usually</strong> — обычно: She usually walks to school.</li>
<li><strong>often</strong> — часто: We often eat pizza.</li>
<li><strong>sometimes</strong> — иногда: He sometimes reads books.</li>
<li><strong>never</strong> — никогда: I never drink coffee.</li>
<li><strong>every day/week/month</strong> — каждый день/неделю/месяц</li>
</ul>

<h3>Примеры</h3>
<table>
<tr><th>Предложение</th><th>Перевод</th></tr>
<tr><td>I work in an office.</td><td>Я работаю в офисе.</td></tr>
<tr><td>She doesn\'t like spiders.</td><td>Она не любит пауков.</td></tr>
<tr><td>Do you speak English?</td><td>Ты говоришь по-английски?</td></tr>
<tr><td>The sun rises in the east.</td><td>Солнце встаёт на востоке.</td></tr>
</table>

<h2>Present Continuous — настоящее длительное</h2>

<h3>Образование: am/is/are + V-ing</h3>
<pre><code>I am working / I\'m working
He is reading / He\'s reading
They are playing / They\'re playing

Отрицательное: I am not working
Вопросительное: Are you working?</code></pre>

<h3>Правила добавления -ing</h3>
<table>
<tr><th>Правило</th><th>Глагол</th><th>-ing форма</th></tr>
<tr><td>Просто + ing</td><td>work, play, read</td><td>working, playing, reading</td></tr>
<tr><td>Бессогласная + гласная → удвоение</td><td>run, sit, make</td><td>running, sitting, making</td></tr>
<tr><td>-e → -ing</td><td>make, write, have</td><td>making, writing, having</td></tr>
<tr><td>-ie → -ying</td><td>die, lie, tie</td><td>dying, lying, tying</td></tr>
</table>

<h3>Сигнальные слова</h3>
<ul>
<li><strong>now</strong> — сейчас: I\'m studying now.</li>
<li><strong>right now</strong> — прямо сейчас</li>
<li><strong>at the moment</strong> — в данный момент</li>
<li><strong>today</strong> — сегодня (если действие происходит сейчас)</li>
<li><strong>Look! / Listen!</strong> — Смотри! / Слушай!</li>
</ul>

<h2>Различия Present Simple и Continuous</h2>
<table>
<tr><th>Present Simple</th><th>Present Continuous</th></tr>
<tr><td>Привычные действия</td><td>Действия в процессе</td></tr>
<tr><td>I drink coffee every morning.</td><td>I\'m drinking coffee now.</td></tr>
<tr><td>Факты и общие истины</td><td>Временные ситуации</td></tr>
<tr><td>Water boils at 100°C.</td><td>Water is boiling now.</td></tr>
<tr><td>Расписания</td><td>Изменения в процессе</td></tr>
<tr><td>The train leaves at 9.</td><td>The price is increasing.</td></tr>
</table>

<h3>Типичные ошибки</h3>
<ul>
<li>❌ I am liking ice cream. → ✅ I like ice cream. (нельзя использовать Continuous с глаголами восприятия)</li>
<li>❌ She works now. → ✅ She is working now. (с now нужен Continuous)</li>
<li>❌ I am agree. → ✅ I agree. (некоторые глаголы не используют Continuous)</li>
</ul>'],

            ['title' => 'Базовая лексика и диалоги', 'type' => 'article', 'module' => 'English лексика', 'difficulty' => 'medium', 'duration_minutes' => 25,
             'description' => 'Семья, еда, транспорт, повседневные фразы, диалоги.',
             'content' => '<h2>Семья и родственники</h2>
<table>
<tr><th>Английский</th><th>Транскрипция</th><th>Перевод</th></tr>
<tr><td>mother</td><td>/ˈmʌðər/</td><td>мама</td></tr>
<tr><td>father</td><td>/ˈfɑːðər/</td><td>папа</td></tr>
<tr><td>sister</td><td>/ˈsɪstər/</td><td>сестра</td></tr>
<tr><td>brother</td><td>/ˈbrʌðər/</td><td>брат</td></tr>
<tr><td>grandmother</td><td>/ˈɡrænmʌðər/</td><td>бабушка</td></tr>
<tr><td>grandfather</td><td>/ˈɡrænfɑːðər/</td><td>дедушка</td></tr>
<tr><td>aunt</td><td>/ænt/</td><td>тётя</td></tr>
<tr><td>uncle</td><td>/ˈʌŋkl/</td><td>дядя</td></tr>
<tr><td>cousin</td><td>/ˈkʌzn/</td><td>двоюродный брат/сестра</td></tr>
</table>

<h2>Еда и напитки</h2>
<table>
<tr><th>Английский</th><th>Перевод</th><th>Английский</th><th>Перевод</th></tr>
<tr><td>breakfast</td><td>завтрак</td><td>bread</td><td>хлеб</td></tr>
<tr><td>lunch</td><td>обед</td><td>milk</td><td>молоко</td></tr>
<tr><td>dinner</td><td>ужин</td><td>coffee</td><td>кофе</td></tr>
<tr><td>water</td><td>вода</td><td>tea</td><td>чай</td></tr>
<tr><td>rice</td><td>рис</td><td>chicken</td><td>курица</td></tr>
<tr><td>apple</td><td>яблоко</td><td>banana</td><td>банан</td></tr>
<tr><td>cheese</td><td>сыр</td><td>egg</td><td>яйцо</td></tr>
</table>

<h2>Транспорт</h2>
<table>
<tr><th>Английский</th><th>Перевод</th><th>Пример</th></tr>
<tr><td>car</td><td>машина</td><td>I go by car.</td></tr>
<tr><td>bus</td><td>автобус</td><td>The bus is late.</td></tr>
<tr><td>train</td><td>поезд</td><td>Take the train to London.</td></tr>
<tr><td>plane</td><td>самолёт</td><td>The plane departs at 9.</td></tr>
<tr><td>bicycle</td><td>велосипед</td><td>I ride my bicycle.</td></tr>
<tr><td>taxi</td><td>такси</td><td>Let\'s take a taxi.</td></tr>
<tr><td>subway</td><td>метро</td><td>Take the subway downtown.</td></tr>
</table>

<h2>Повседневные фразы</h2>
<ul>
<li><strong>I like...</strong> — Я люблю...: I like music.</li>
<li><strong>I don\'t like...</strong> — Я не люблю...: I don\'t like spiders.</li>
<li><strong>Can I...?</strong> — Могу я...?: Can I have some water?</li>
<li><strong>I would like...</strong> — Я хотел бы...: I would like a coffee, please.</li>
<li><strong>I need...</strong> — Мне нужно...: I need a new phone.</li>
<li><strong>I want...</strong> — Я хочу...: I want to learn English.</li>
</ul>

<h2>Диалог: В магазине</h2>
<pre><code>Shopkeeper: Hello! Can I help you?
Customer:  Yes, please. I\'d like some bread.
Shopkeeper: White or brown?
Customer:  White, please. And a bottle of milk.
Shopkeeper: Anything else?
Customer:  No, that\'s all. How much is it?
Shopkeeper: That\'ll be £3.50, please.
Customer:  Here you are.
Shopkeeper: Thank you! Have a nice day!</code></pre>

<h2>Диалог: Знакомство</h2>
<pre><code>Anna:    Hi! My name is Anna. What\'s your name?
David:   Hi Anna! I\'m David. Nice to meet you!
Anna:    Nice to meet you too! Where are you from?
David:   I\'m from London. And you?
Anna:    I\'m from Moscow. Do you live here?
David:   Yes, I do. I work at a university.
Anna:    What do you do there?
David:   I\'m a teacher. What about you?
Anna:    I\'m a student. I study English.</code></pre>'],

            ['title' => 'Тест по английскому A1', 'type' => 'quiz', 'module' => 'English тест', 'difficulty' => 'medium', 'duration_minutes' => 15,
             'description' => 'Итоговый тест по английскому A1.',
             'content' => '<h2>Тест по английскому A1</h2>'],
        ],
    ],
];
