# BlaBlaArticle - финальная работа по курсу SkillBox.Symfony

## BlaBlaArticle - web-сервис генерации статей на выбранную тему с заданными пользователем параметрами

### Публичная страница - реализованный функционал 
1. Однократная пробная генерация статьи. При регистрации пользователя сгенерированная пробная статья сохраняется, как статья этого пользователя.
2. Авторизация
3. Регистрация
4. Валидация форм 

### Приватные страницы - реализованный функционал
5. Отображение сводной информации о пользователе (количество созданных статей всего и за период, уровень подписки)
6. Генерация статьи на выбранную тему с заданными параметрами (ключевые слова в соответствующих падежах, размер статьи в абзацах, использование загруженных изображений пользователя)
7. Отображение всей истории статей с переходом к статье и возможности её повторной генерации по сохранённым параметрам статьи
8. Выбор уровня подписки, который влияет на количество создаваемых статей, а так же иные возможности. Выбранный и сохранённый пользователем инстанс подписки не зависит от текущих изменений возможностей подписки.  
9. Генерация токена для создания статьи по API
10. Добавление пользовательских шаблонов html-разметки для статьи

### API
11. Зарегистрированные пользователи могут генерировать стать по API с соблюдением возможностей и ограничений выбранного уровня подписки

### Консольные команды
12. Проверка срока действия подписок пользователей и сброс на дефолтную подписку
13. Создание/обновление уровней подписки

### Информирование пользователей по email
14. Об успешной регистрации и необходимости подтверждения email
15. О выборе новой подписки или об автоматическом сбросе на дефолтную подписку
