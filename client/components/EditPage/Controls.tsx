import './Controls.scss';
import React from 'react';
import { toJunction } from 'brookjs';
import { Observable } from 'kefir';
import { i18n, link } from '../../helpers';
import {
  editorTabsToggle,
  editorThemeChange,
  editorInvisiblesToggle,
  editorStatusChange,
  editorSyncToggle,
  editorUpdateClick,
  editorAddClick,
} from '../../actions';
import { Toggle } from '../../api';
import { editorWidthChange } from '../../editor/actions';

const mapCheckedToString = (e: React.ChangeEvent<HTMLInputElement>): Toggle =>
  e.target.checked ? 'on' : 'off';

type Status = {
  slug: string;
  name: string;
};

type Theme = {
  slug: string;
  name: string;
};

type Width = {
  slug: string;
  name: string;
};

type Props = {
  statuses: Status[];
  themes: Theme[];
  widths: Width[];
  gist: {
    show: boolean;
    url?: string;
  };
  sync: Toggle;
  tabs: Toggle;
  invisibles: Toggle;
  selectedTheme: string;
  selectedStatus: string;
  selectedWidth: string;
  onStatusChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
  onSyncChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onThemeChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
  onTabsChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onWidthChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
  onInvisiblesChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onUpdateClick: (e: React.MouseEvent<HTMLButtonElement>) => void;
  onAddClick: (e: React.MouseEvent<HTMLButtonElement>) => void;
};

const toggleToBoolean = (toggle: Toggle): boolean => toggle === 'on';

const ButtonControl: React.FC<{
  className: string;
  onClick: (e: React.MouseEvent<HTMLButtonElement>) => void;
  children: React.ReactNode;
}> = ({ className, onClick, children }) => (
  <div className="wpgp-editor-control">
    <button
      className={`dashicons-before wpgp-button ${className}`}
      onClick={onClick}
    >
      {children}
    </button>
  </div>
);

const Controls: React.FC<Props> = ({
  selectedTheme,
  selectedStatus,
  selectedWidth,
  widths,
  statuses,
  sync,
  themes,
  tabs,
  invisibles,
  gist,
  onStatusChange,
  onSyncChange,
  onThemeChange,
  onTabsChange,
  onWidthChange,
  onInvisiblesChange,
  onUpdateClick,
  onAddClick,
}) => (
  <div className={`wpgp-editor-controls wpgp-editor-controls-${selectedTheme}`}>
    <div className="wpgp-editor-control">
      <label htmlFor="wpgp-editor-status">{i18n('editor.status')}: </label>
      <select
        id="wpgp-editor-status"
        value={selectedStatus}
        onChange={onStatusChange}
      >
        {statuses.map(({ slug, name }) => (
          <option value={slug} key={slug}>
            {name}
          </option>
        ))}
      </select>
    </div>

    <div className="wpgp-editor-control">
      <label htmlFor="wpgp-editor-sync">{i18n('editor.sync')}</label>
      <input
        type="checkbox"
        id="wpgp-editor-sync"
        checked={toggleToBoolean(sync)}
        onChange={onSyncChange}
      />
    </div>

    <div className="wpgp-editor-control">
      <label htmlFor="wpgp-editor-theme">{i18n('editor.theme')}: </label>
      <select
        id="wpgp-editor-theme"
        value={selectedTheme}
        onChange={onThemeChange}
      >
        {themes.map(({ slug, name }) => (
          <option value={slug} key={slug}>
            {name}
          </option>
        ))}
      </select>
    </div>

    <div className="wpgp-editor-control">
      <label htmlFor="wpgp-enable-tabs">{i18n('editor.tabs')} </label>
      <input
        type="checkbox"
        id="wpgp-enable-tabs"
        checked={toggleToBoolean(tabs)}
        onChange={onTabsChange}
      />
    </div>

    <div className="wpgp-editor-control">
      <label htmlFor="wpgp-editor-width">{i18n('editor.width')}: </label>
      <select
        id="wpgp-editor-width"
        value={selectedWidth}
        onChange={onWidthChange}
      >
        {widths.map(({ slug, name }) => (
          <option value={slug} key={slug}>
            {name}
          </option>
        ))}
      </select>
    </div>

    <div className="wpgp-editor-control">
      <label htmlFor="wpgp-enable-invisibles">
        {i18n('editor.invisibles')}{' '}
      </label>
      <input
        type="checkbox"
        id="wpgp-enable-invisibles"
        checked={toggleToBoolean(invisibles)}
        onChange={onInvisiblesChange}
      />
    </div>

    <ButtonControl className="wpgp-button-update" onClick={onUpdateClick}>
      {i18n('editor.update')}
    </ButtonControl>

    <ButtonControl className="wpgp-button-add" onClick={onAddClick}>
      {i18n('editor.file.add')}
    </ButtonControl>

    <div className="wpgp-editor-control">
      <a
        href={link('wpgp_route', 'commits')}
        className="dashicons-before wpgp-button wpgp-button-add"
      >
        {i18n('editor.commits')}
      </a>
      {gist.show ? (
        <a
          href={gist.url}
          className="dashicons-before wpgp-button wpgp-button-add"
        >
          {i18n('editor.gist')}
        </a>
      ) : null}
    </div>
  </div>
);

const events = {
  onStatusChange: (
    e$: Observable<React.ChangeEvent<HTMLSelectElement>, never>,
  ) => e$.map(e => editorStatusChange(e.target.value)),
  onSyncChange: (e$: Observable<React.ChangeEvent<HTMLInputElement>, never>) =>
    e$.map(e => editorSyncToggle(mapCheckedToString(e))),
  onThemeChange: (
    e$: Observable<React.ChangeEvent<HTMLSelectElement>, never>,
  ) => e$.map(e => editorThemeChange(e.target.value)),
  onTabsChange: (e$: Observable<React.ChangeEvent<HTMLInputElement>, never>) =>
    e$.map(e => editorTabsToggle(mapCheckedToString(e))),
  onWidthChange: (
    e$: Observable<React.ChangeEvent<HTMLSelectElement>, never>,
  ) => e$.map(e => editorWidthChange(Number(e.target.value))),
  onInvisiblesChange: (
    e$: Observable<React.ChangeEvent<HTMLInputElement>, never>,
  ) => e$.map(e => editorInvisiblesToggle(mapCheckedToString(e))),
  onUpdateClick: (e$: Observable<React.MouseEvent<HTMLButtonElement>, never>) =>
    e$.map(e => {
      e.preventDefault();
      return editorUpdateClick();
    }),
  onAddClick: (e$: Observable<React.MouseEvent<HTMLButtonElement>, never>) =>
    e$.map(e => {
      e.preventDefault();
      return editorAddClick();
    }),
};

export default toJunction(events)(Controls);
