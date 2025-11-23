# EventFlow AI
**Sistema Web baseado em Arquitetura Orientada a Eventos com Integração de IA, Mensageria e Testes Automatizados utilizando Laravel**

O **EventFlow AI** é um sistema web desenvolvido com o framework **Laravel**, adotando uma **Arquitetura Orientada a Eventos (Event-Driven Architecture — EDA)** e incorporando técnicas modernas de mensageria, processamento assíncrono, testes automatizados e integração com agentes de Inteligência Artificial.  
Este projeto tem como objetivo demonstrar, de forma prática e aplicada, como conceitos arquiteturais contemporâneos podem ser combinados para criar aplicações web escaláveis, desacopladas e inteligentes.

---

## 🎯 Objetivos do Projeto

- Implementar um sistema orientado a eventos utilizando os recursos nativos do Laravel.
- Utilizar mensageria e filas (Redis) para processamento assíncrono.
- Integrar serviços de IA (como modelos LLM ou agentes) para análise automática de mensagens.
- Aplicar testes automatizados (Pest e PHPUnit) para garantir qualidade e confiabilidade.
- Demonstrar boas práticas de arquitetura, DevOps e engenharia de software.
- Servir como base prática para artigo científico utilizando o modelo ABNT.

---

## 🏗 Arquitetura Geral

O sistema segue uma abordagem **EDA (Event-Driven Architecture)** composta por:

- **Eventos** → responsáveis por representar mudanças de estado (ex.: `MessageCreated`, `MessageProcessed`).
- **Filas e Jobs** → processamento assíncrono usando Redis.
- **Serviço de IA** → responsável por análise e enriquecimento das mensagens.
- **API REST** → CRUD básico para interação com mensagens.
- **Testes Automatizados** → cadeira completa de testes Feature e Unit com Pest.

Fluxo resumido:

1. Usuário envia uma mensagem via API → `POST /api/messages`
2. Um evento `MessageCreated` é disparado
3. O job `ProcessMessageJob` é enfileirado
4. A IA analisa a mensagem e retorna dados processados
5. A entidade é atualizada e o evento `MessageProcessed` é disparado
6. Resultados podem ser consultados via API

---

## 📂 Estrutura do Projeto (simplificada)


---

## ⚙️ Tecnologias Utilizadas

- **PHP 8.x**
- **Laravel 10**
- **Redis** (mensageria / filas)
- **Pest + PHPUnit** (testes)
- **Docker** (opcional para desenvolvimento)
- **Guzzle** (integração com IA externa)
- **OpenAI / Ollama** (opcional — para IA)

---

## 🚀 Execução do Projeto

### 1. Instalar dependências
```bash
composer install
