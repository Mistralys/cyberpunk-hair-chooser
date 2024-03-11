# Cyberpunk 2077 Hair Chooser

PHP tool to choose hairs from multiple mods, and create custom hair mods 
to easily select among the available hairstyles.

## What does it do?

The tool looks through all your hair mods, and gives you the possibility
to select which hairstyle to use for each available hair slot. It then
creates a custom mod that can be loaded with your preferred mod manager 
to make them available in the game.

## Why is this needed?

Available hairstyles in Cyberpunk 2077 are fixed: It is only possible 
to replace existing hairstyles, not add new ones. 

Additionally, hairstyle mods are created for a specific numbered hair 
slot, so if you want to use multiple hairstyle mods at the same time,
you need to check which slots the mods replace to avoid conflicts.

This tool aims to simplify the process.

## Requirements

- Webserver with [PHP](https://php.net) 8.2 or higher
- [Composer](https://getcomposer.org/)

## Installation

1. Clone this repository.
2. Run `composer install` to install dependencies.
3. Copy `config/paths.dist.php` to `config/paths.php` and adjust the settings.

## How to use

1. Open the tool in your web browser.
2. In the "Selected downloads" tab, select the hair mods ZIP files you have installed.
3. Extract them in the "Extract files" tab.
4. In the "Hair archives" tab, specify the hair slot numbers for the hairstyles in the mods (only some can be auto-detected).
5. In the "Add mod" tab, create a configuration for the custom mod you want to create.
6. Once added, via the "Mods" tab, you can build a mod ZIP file.

## Hairstyle preview screenshots

### Supported hair mods

I have added self-made preview images for the hairs in the following mods:

- [Wingdeer Hair Collection](https://www.nexusmods.com/cyberpunk2077/mods/6072?tab=files)
  - Batsy
  - Commissioner
  - Daniels
  - Emma3
  - January
  - Lee
  - Manavortex
  - Mid Length Pack
  - Shorty Pack
  - Sorceress Pack All In One
  - Tarnished Pack
  - Tide Up Pack
  - Valeria
- [Bayonetta 2](https://www.nexusmods.com/cyberpunk2077/mods/13409)
- [Sori Yumi's Hairstyle Collection](https://www.nexusmods.com/cyberpunk2077/mods/2636)

### How to add more

Assuming you have found or created a screenshot for a hairstyle, you can add it 
to the tool by following these steps:

1. Open the hair mods extraction folder (as configured in `paths.php`).
2. Find the hairstyle folder, and open it.
3. Place the screenshot in the same folder.
4. Rename the screenshot to the same name as the `.archive` file.

**Example:**

Folder for Wingdeer's Commissioner hairstyle:

```
Commissioner Hair - PHYSCIS ENABLED-6072-1-0-1670681702-zip
```

Archive name:

```
#WINGDEER_FemV_HAIR_COMMISSIONER_NO1.archive
```

Screenshot name:

```
#WINGDEER_FemV_HAIR_COMMISSIONER_NO1.png
```

> Supported image file types are `png`, `gif`, `jpg`, `jpeg` and `webp`.
