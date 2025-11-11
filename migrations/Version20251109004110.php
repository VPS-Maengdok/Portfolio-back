<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109004110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Locales
        $this->addSql("INSERT INTO locale (id, label, shortened, datetime_immutable) VALUES
        (1, 'Français', 'fr', NOW()),
        (2, 'English', 'en', NOW()),
        (3, '한국어', 'ko', NOW())
        ON CONFLICT DO NOTHING");

        // Statuses
        $this->addSql("INSERT INTO status (id, datetime_immutable) VALUES
        (1, NOW()),(2, NOW()),(3, NOW()),
        (4, NOW()),(5, NOW()),(6, NOW()),
        (7, NOW()),(8, NOW()),(9, NOW())
        ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO status_i18n (id, status_id, label, locale_id, datetime_immutable) VALUES
        -- fr
        (1, 1, 'Production',       1, NOW()),
        (2, 2, 'En construction',  1, NOW()),
        (3, 3, 'Annulé',           1, NOW()),
        -- en
        (4, 1, 'Production',       2, NOW()),
        (5, 2, 'Work in progress', 2, NOW()),
        (6, 3, 'Cancelled',        2, NOW()),
        -- ko
        (7, 1, '운영 중',           3, NOW()),
        (8, 2, '개발 중',           3, NOW()),
        (9, 3, '중단됨',            3, NOW())
        ON CONFLICT DO NOTHING");

        // Tags
        $this->addSql("INSERT INTO tag (id, datetime_immutable) VALUES 
        (1,NOW()),(2,NOW()),(3,NOW()),(4,NOW()),(5,NOW()),
        (6,NOW()),(7,NOW()),(8,NOW()),(9,NOW()),(10,NOW()),
        (11,NOW()),(12,NOW()),(13,NOW()),(14,NOW()),(15,NOW()),
        (16,NOW()),(17,NOW()),(18,NOW()),(19,NOW()),(20,NOW()),
        (21,NOW()),(22,NOW()),(23,NOW()),(24,NOW()),(25,NOW()),
        (26,NOW()),(27,NOW()),(28,NOW()),(29,NOW()),(30,NOW())
        ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO tag_i18n (id, tag_id, label, locale_id, datetime_immutable) VALUES
        -- fr
        (1, 1,  'Back End',         1, NOW()),
        (2, 2,  'Front End',        1, NOW()),
        (3, 3,  'DevOps',           1, NOW()),
        (4, 4,  'Base de données',  1, NOW()),
        (5, 5,  'Mobile',           1, NOW()),
        (6, 6,  'IA',               1, NOW()),
        (7, 7,  'Blockchain',       1, NOW()),
        (8, 8,  'Sécurité',         1, NOW()),
        (9, 9,  'Cloud',            1, NOW()),
        (10,10, 'Autre',            1, NOW()),
        -- en
        (11, 1, 'Back End',         2, NOW()),
        (12, 2, 'Front End',        2, NOW()),
        (13, 3, 'DevOps',           2, NOW()),
        (14, 4, 'Database',         2, NOW()),
        (15, 5, 'Mobile',           2, NOW()),
        (16, 6, 'AI',               2, NOW()),
        (17, 7, 'Blockchain',       2, NOW()),
        (18, 8, 'Security',         2, NOW()),
        (19, 9, 'Cloud',            2, NOW()),
        (20,10, 'Other',            2, NOW()),
        -- ko
        (21, 1, '백엔드',             3, NOW()),
        (22, 2, '프론트엔드',          3, NOW()),
        (23, 3, '데브옵스',           3, NOW()),
        (24, 4, '데이터베이스',        3, NOW()),
        (25, 5, '모바일',            3, NOW()),
        (26, 6, '인공지능',           3, NOW()),
        (27, 7, '블록체인',           3, NOW()),
        (28, 8, '보안',              3, NOW()),
        (29, 9, '클라우드',           3, NOW()),
        (30,10, '기타',              3, NOW())
        ON CONFLICT DO NOTHING");

        // Technologies
        $this->addSql("INSERT INTO technology (id, icon, label, datetime_immutable) VALUES
        (1,'icon-php',                      'PHP',                      NOW()),
        (2,'icon-javascript',               'JavaScript',               NOW()),
        (3,'icon-python',                   'Python',                   NOW()),
        (4,'icon-java',                     'Java',                     NOW()),
        (5,'icon-csharp',                   'C#',                       NOW()),
        (6,'icon-cplusplus',                'C++',                      NOW()),
        (7,'icon-c',                        'C',                        NOW()),
        (8,'icon-ruby',                     'Ruby',                     NOW()),
        (9,'icon-go',                       'Go',                       NOW()),
        (10,'icon-symfony',                 'Symfony',                  NOW()),
        (11,'icon-laravel',                 'Laravel',                  NOW()),
        (12,'icon-react',                   'React',                    NOW()),
        (13,'icon-vuejs',                   'Vue.js',                   NOW()),
        (14,'icon-angular',                 'Angular',                  NOW()),
        (15,'icon-nodejs',                  'Node.js',                  NOW()),
        (16,'icon-express',                 'Express',                  NOW()),
        (17,'icon-mongodb',                 'MongoDB',                  NOW()),
        (18,'icon-mysql',                   'MySQL',                    NOW()),
        (19,'icon-postgresql',              'PostgreSQL',               NOW()),
        (20,'icon-sqlite',                  'SQLite',                   NOW()),
        (21,'icon-oracle',                  'Oracle',                   NOW()),
        (22,'icon-microsoft-sql-server',    'Microsoft SQL Server',     NOW()),
        (23,'icon-docker',                  'Docker',                   NOW()),
        (24,'icon-kubernetes',              'Kubernetes',               NOW()),
        (25,'icon-git',                     'Git',                      NOW()),
        (26,'icon-github',                  'GitHub',                   NOW()),
        (27,'icon-gitlab',                  'GitLab',                   NOW()),
        (28,'icon-bitbucket',               'Bitbucket',                NOW()),
        (29,'icon-jira',                    'Jira',                     NOW()),
        (30,'icon-trello',                  'Trello',                   NOW()),
        (31,'icon-asana',                   'Asana',                    NOW()),
        (32,'icon-typescript',              'Typescript',               NOW()),
        (33,'icon-nextjs',                  'Next.js',                  NOW()),
        (34,'icon-nuxtjs',                  'Nuxt.js',                  NOW()),
        (35,'icon-tailwindcss',             'Tailwind CSS',             NOW()),
        (36,'icon-bootstrap',               'Bootstrap',                NOW()),
        (37,'icon-materialui',              'Material UI',              NOW()),
        (38,'icon-antdesign',               'Ant Design',               NOW()),
        (39,'icon-chakraui',                'Chakra UI',                NOW()),
        (40,'icon-styledcomponents',        'Styled Components',        NOW()),
        (41,'icon-redux',                   'Redux',                    NOW()),
        (42,'icon-reactnative',             'React Native',             NOW()),
        (43,'icon-flutter',                 'Flutter',                  NOW()),
        (44,'icon-swift',                   'Swift',                    NOW()),
        (45,'icon-kotlin',                  'Kotlin',                   NOW()),
        (46,'icon-objectivec',              'Objective-C',              NOW()),
        (47,'icon-php',                     'Zend Framework',           NOW())
        ON CONFLICT DO NOTHING");

        // Skills
        $this->addSql("INSERT INTO skill (id, datetime_immutable) VALUES
        (1,NOW()),(2,NOW()),(3,NOW()),(4,NOW()),(5,NOW()),
        (6,NOW()),(7,NOW()),(8,NOW()),(9,NOW()),(10,NOW()),
        (11,NOW()),(12,NOW()),(13,NOW()),(14,NOW()),(15,NOW()),
        (16,NOW()),(17,NOW()),(18,NOW())
        ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO skill_i18n (id, skill_id, label, locale_id, datetime_immutable) VALUES
        -- fr
        (1, 1, 'Chef de projet',      1, NOW()),
        (2, 2, 'Power BI',            1, NOW()),
        (3, 3, 'Figma',               1, NOW()),
        (4, 4, 'Gestion de projet',   1, NOW()),
        (5, 5, 'Microsoft Graph',     1, NOW()),
        (6, 6, 'OpenAI',              1, NOW()),
        -- en
        (7, 1, 'Project Manager',     2, NOW()),
        (8, 2, 'Power BI',            2, NOW()),
        (9, 3, 'Figma',               2, NOW()),
        (10,4, 'Project management',  2, NOW()),
        (11,5, 'Microsoft Graph',     2, NOW()),
        (12,6, 'OpenAI',              2, NOW()),
        -- ko
        (13,1, '프로젝트 매니저',         3, NOW()),
        (14,2, 'Power BI',            3, NOW()),
        (15,3, 'Figma',               3, NOW()),
        (16,4, '프로젝트 관리',          3, NOW()),
        (17,5, 'Microsoft Graph',     3, NOW()),
        (18,6, 'OpenAI',              3, NOW())
        ON CONFLICT DO NOTHING");

        // Work Types
        $this->addSql("INSERT INTO work_type (id, datetime_immutable) VALUES
        (1,NOW()),(2,NOW()),(3,NOW()),
        (4,NOW()),(5,NOW()),(6,NOW()),
        (7,NOW()),(8,NOW()),(9,NOW())
        ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO work_type_i18n (id, work_type_id, label, locale_id, datetime_immutable) VALUES
        -- fr
        (1, 1, 'Sur site',            1, NOW()),
        (2, 2, 'Hybride',             1, NOW()),
        (3, 3, 'Distanciel',          1, NOW()),
        -- en
        (4, 1, 'On Site',             2, NOW()),
        (5, 2, 'Hybride',             2, NOW()),
        (6, 3, 'Remote',              2, NOW()),
        -- ko
        (7, 1, '상주 근무',             3, NOW()),
        (8, 2, '하이브리드 근무',         3, NOW()),
        (9, 3, '재택 근무',             3, NOW())
        ON CONFLICT DO NOTHING");

        // Languages
        $this->addSql("INSERT INTO language (id, datetime_immutable) VALUES
        (1,NOW()),(2,NOW()),(3,NOW()),
        (4,NOW()),(5,NOW()),(6,NOW()),
        (7,NOW()),(8,NOW()),(9,NOW())
        ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO language_i18n (id, language_id, label, level, locale_id, datetime_immutable) VALUES
        -- fr
        (1, 1, 'Français', 'Natif',  1, NOW()),
        (2, 2, 'Anglais',  'B2+',    1, NOW()),
        (3, 3, 'Coréen',   'A2+',    1, NOW()),
        -- en
        (4, 1, 'French',   'Native', 2, NOW()),
        (5, 2, 'English',  'B2+',    2, NOW()),
        (6, 3, 'Korean',   'A2+',    2, NOW()),
        -- ko
        (7, 1, '프랑스어',  '모국어',    3, NOW()),
        (8, 2, '영어',     'B2+',     3, NOW()),
        (9, 3, '한국어',    'A2+',     3, NOW())
        ON CONFLICT DO NOTHING");

        // Countries
        $this->addSql("INSERT INTO country (id, datetime_immutable) VALUES
        (1,NOW()),(2,NOW()),(3,NOW()),(4,NOW()),(5,NOW()),
        (6,NOW()),(7,NOW()),(8,NOW()),(9,NOW()),(10,NOW()),
        (11,NOW()),(12,NOW()),(13,NOW()),(14,NOW()),(15,NOW()),
        (16,NOW()),(17,NOW()),(18,NOW()),(19,NOW()),(20,NOW()),
        (21,NOW())
        ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO country_i18n (id, country_id, label, locale_id, datetime_immutable) VALUES
        -- fr
        (1, 1, 'France',        1, NOW()),
        (2, 2, 'Corée du Sud',  1, NOW()),
        (3, 3, 'Japon',         1, NOW()),
        (4, 4, 'Vietnam',       1, NOW()),
        (5, 5, 'Chine',         1, NOW()),
        (6, 6, 'Taïwan',        1, NOW()),
        (7, 7, 'Singapour',     1, NOW()),
        -- en
        (8, 1, 'France',        2, NOW()),
        (9, 2, 'South Korea',   2, NOW()),
        (10,3, 'Japan',         2, NOW()),
        (11,4, 'Vietnam',       2, NOW()),
        (12,5, 'China',         2, NOW()),
        (13,6, 'Taiwan',        2, NOW()),
        (14,7, 'Singapore',     2, NOW()),
        -- ko
        (15,1, '프랑스',         3, NOW()),
        (16,2, '대한민국',       3, NOW()),
        (17,3, '일본',           3, NOW()),
        (18,4, '베트남',         3, NOW()),
        (19,5, '중국',           3, NOW()),
        (20,6, '대만',           3, NOW()),
        (21,7, '싱가포르',       3, NOW())
        ON CONFLICT DO NOTHING");

        foreach ([
            'locale',
            'status',
            'tag',
            'technology',
            'skill',
            'work_type',
            'language',
            'country',
            'status_i18n',
            'tag_i18n',
            'skill_i18n',
            'work_type_i18n',
            'language_i18n',
            'country_i18n',
        ] as $table) {
            $this->addSql("
                SELECT setval(
                    pg_get_serial_sequence('{$table}', 'id'),
                    COALESCE((SELECT MAX(id) FROM {$table}), 0),
                    true
                )
            ");
        }
    }

    public function down(Schema $schema): void
    {
        // Countries
        $this->addSql("DELETE FROM country_i18n");
        $this->addSql("DELETE FROM country");

        // Languages
        $this->addSql("DELETE FROM language_i18n");
        $this->addSql("DELETE FROM language");

        // Work Types
        $this->addSql("DELETE FROM work_type_i18n");
        $this->addSql("DELETE FROM work_type");

        // Skills
        $this->addSql("DELETE FROM skill_i18n");
        $this->addSql("DELETE FROM skill");

        // Technologies
        $this->addSql("DELETE FROM technology");

        // Tags
        $this->addSql("DELETE FROM tag_i18n");
        $this->addSql("DELETE FROM tag");

        // Status
        $this->addSql("DELETE FROM status_i18n");
        $this->addSql("DELETE FROM status");

        // Locales
        $this->addSql("DELETE FROM locale");
    }
}
