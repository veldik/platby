# platby

Backend v Laravelu k systému platby.
Frontend je v repozitáři [platby-frontend](https://github.com/veldik/platby-frontend).


## Funkce
- Vytváření plateb s e-mailovým upozorněním a generováním QR kódu
- Možnost vytvořit platbu pro více uživatelů najednou
- Sledování příchozích plateb
- Automatické upozornění uživatelů na nezaplacené platby
- Automatické periodické vytváření plateb s nastavitelnou periodou
- Možnost předplatit kredity pro budoucí platby
- Možnost registrace uživatelů pro zobrazení jejich plateb a dobití kreditů

## Vývoj

Pro testování funkčnosti bez použití reálného napojení na banku lze využít [platby-banktest](https://github.com/veldik/platby-banktest).
