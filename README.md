# 🍬 Doce Encanto — E-commerce de Doceria (Flutter & PHP)

O projeto da doceria fictícia **Doce Encanto** agora conta com uma experiência completa em **Flutter**, mantendo os arquivos PHP originais como referência. A nova aplicação Flutter foi criada para funcionar tanto em dispositivos móveis quanto na web, trazendo uma interface moderna e responsiva que replica o comportamento do site anterior.

---

## 🚀 Tecnologias Utilizadas

### Nova aplicação
- **Flutter 3** — Base do aplicativo multiplataforma (mobile/web)
- **Provider** — Gerenciamento simples de estado para o carrinho
- **Google Fonts** — Tipografia com a fonte Cookie e Inter

### Referência original
- **PHP 8+**, **HTML5/CSS3** e **JavaScript** (mínimo)

---

## 💻 Funcionalidades Principais (Flutter)

- Página inicial com banner hero, destaques, seção “Sobre” e depoimentos
- Catálogo de produtos com filtro por categorias
- Carrinho de compras com atualização de quantidades
- Página de checkout com formulário validado e resumo do pedido
- Tema responsivo com suporte a modo claro/escuro

---

## 🧁 Estrutura do Projeto

```
.
├─ assets/                # Imagens utilizadas em ambas as versões
├─ lib/
│  ├─ data.dart           # Dados estáticos (produtos, categorias, depoimentos)
│  ├─ models.dart         # Modelos das entidades principais
│  ├─ app_state.dart      # Lógica do carrinho usando ChangeNotifier
│  ├─ main.dart           # Ponto de entrada Flutter com rotas e tema
│  ├─ pages/              # Telas (home, produtos, carrinho, checkout)
│  └─ widgets/            # Componentes reutilizáveis (cartões de produto)
├─ pubspec.yaml           # Configuração do projeto Flutter
├─ analysis_options.yaml  # Regras de lint adicionais
├─ index.php, cart.php…   # Implementação PHP original mantida como referência
└─ README.md
```

---

## ▶️ Executando a versão Flutter

1. Certifique-se de ter o [Flutter SDK](https://docs.flutter.dev/get-started/install) instalado.
2. Instale as dependências:
   ```bash
   flutter pub get
   ```
3. Para executar em um dispositivo/emulador móvel:
   ```bash
   flutter run
   ```
4. Para executar no navegador (Flutter Web habilitado):
   ```bash
   flutter run -d chrome
   ```

---

## 🗂️ Versão PHP (legado)

Os arquivos PHP originais continuam disponíveis nas pastas `includes/`, `assets/` e nas páginas `index.php`, `products.php`, `cart.php` e `checkout.php`. Eles podem ser utilizados como referência ou executados em um ambiente com servidor PHP.

---

## 📄 Licença

Projeto criado para fins educacionais e demonstração. Utilize, adapte e personalize conforme necessário.
