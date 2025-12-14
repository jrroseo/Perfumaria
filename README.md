# E-Commerce Web App 

*Gostaria de agradecer a todos vocês que deram estrelas e fizeram fork do repositório.

## Primeiros Passos

E-Commerce Web App é um modelo de e-commerce completo e multifuncional, desenvolvido com as melhores práticas. Será a solução perfeita para sua loja atual ou futura, personalizável para qualquer tipo de negócio, com responsividade e uma ótima interface de usuário. Testado em navegadores compatíveis.

## 📁 Conjunto de tecnologias

- **Backend**: PHP
- **Frontend**: Bootstrap 5, jQuery
- **Banco de dados**: MySQL
- **Sessão**: Sessões nativas do PHP
- **Implantação**: Compatível com hospedagem compartilhada e desenvolvimento local (Laragon)
- **HTML** - Versão HTML do modelo

**src/scss/abstracts**

A pasta **abstracts** reúne todas as ferramentas e auxiliares Sass usadas em todo o projeto. Todas as variáveis ​​globais, funções e mixins devem ser colocados aqui. A regra geral para esta pasta é que ela não deve gerar nenhuma linha de CSS quando compilada sozinha. Esses arquivos são apenas auxiliares do Sass. Dentro desta pasta, há outra pasta chamada **mixins-module**. O arquivo nesta pasta contém um mixin que gera classes utilitárias para espaços.

**src/scss/utility.scss**

Este arquivo invoca um mixin do **mixins-module** que gera classes utilitárias para espaços. Essas classes utilitárias também são chamadas de **classes utilitárias de baixo nível** e facilitam a criação de interfaces de usuário complexas.

**src/scss/base**

A pasta **base** contém o que podemos chamar de código boilerplate do projeto. Nela, você pode encontrar algumas regras tipográficas, definindo estilos padrão para elementos HTML comumente usados. **src/scss/components**

**Componentes** são estilos reutilizáveis ​​no layout. Contém todos os tipos de módulos específicos, como botões, caixas de seleção e outros elementos semelhantes. Geralmente, existem muitas pastas, já que todo o site/aplicativo deve ser composto principalmente de pequenos módulos. `_all.scss` é um arquivo de importação que contém todo o código-fonte da pasta. Abaixo, você verá todos os arquivos de componentes e seu uso em diferentes partes do layout.

## 📝 License

Este projeto é de código aberto e de uso gratuito.
