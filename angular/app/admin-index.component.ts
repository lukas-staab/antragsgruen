import {Component, ElementRef} from '@angular/core';
import {WebsocketService} from "./websocket.service";
import {Collection} from "../classes/Collection";
import {Motion} from "../classes/Motion";
import {debounceTime} from 'rxjs/operators';
import {Amendment} from "../classes/Amendment";
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {CollectionItem} from "../classes/CollectionItem";
import {IMotion} from "../classes/IMotion";
import {SelectlistItem} from "./selectlist.component";
import {Translations} from "../classes/Translations";
import {STATUS} from "../classes/Status";

@Component({
    selector: 'admin-index',
    templateUrl: './admin-index.component.html',
})
export class AdminIndexComponent {
    public log: string = '';
    public motionCollection: Collection<Motion> = new Collection<Motion>(Motion);
    public amendmentCollection: Collection<Amendment> = new Collection<Amendment>(Amendment);
    public allItems: IMotion[];
    public sortedFilteredItems: IMotion[];

    public searchPrefix = "";
    public searchTitle = "";
    public searchInitiator = "";
    private filters: { [filterId: string]: (IMotion) => boolean } = {};

    public hasTopics = true;
    public hasProposedProcedure = false;

    private readonly ajaxBackendUrl: string;
    private readonly csrfParam: string;
    private readonly csrfToken: string;
    public readonly linkTemplates: { [key: string]: string };

    public constructor(private _websocket: WebsocketService,
                       private el: ElementRef<Element>,
                       private _http: HttpClient) {
        // Debounce: if a collection comes, don't recalculate the UI for each element
        this.motionCollection.changed$.pipe(debounceTime(1)).subscribe(this.recalcMotionList.bind(this));
        this.amendmentCollection.changed$.pipe(debounceTime(1)).subscribe(this.recalcMotionList.bind(this));

        if (el.nativeElement.getAttribute('ws-port')) {
            this.initWebsocket(el);
        }
        this._websocket.setSubdomainPath(el.nativeElement.getAttribute('subdomain'), el.nativeElement.getAttribute('path'));
        this.ajaxBackendUrl = el.nativeElement.getAttribute('ajax-backend');
        this.csrfToken = el.nativeElement.getAttribute('csrf-token');
        this.csrfParam = el.nativeElement.getAttribute('csrf-param');
        this.linkTemplates = JSON.parse(el.nativeElement.getAttribute('link-templates'));
        let initData = JSON.parse(el.nativeElement.getAttribute('init-collections'));
        this.motionCollection.setElements(initData['motions']);
        this.amendmentCollection.setElements(initData['amendments']);
    }

    private initWebsocket(el: ElementRef) {
        this._websocket.debuglog$.subscribe((str) => {
            this.log += str + "\n";
        });
        this._websocket.authenticated$.subscribe((user) => {
            this._websocket.subscribeCollectionChannel(1, 'motions', this.motionCollection);
            this._websocket.subscribeCollectionChannel(1, 'amendments', this.amendmentCollection);
        });
        this._websocket.connect(
            el.nativeElement.getAttribute('cookie'),
            el.nativeElement.getAttribute('ws-port')
        );
    }

    private recalcMotionList() {
        this.allItems = [];
        Object.keys(this.motionCollection.elements).forEach(key => {
            this.allItems.push(this.motionCollection.elements[key]);
        });
        Object.keys(this.amendmentCollection.elements).forEach(key => {
            this.allItems.push(this.amendmentCollection.elements[key]);
        });

        this.sortedFilteredItems = this.allItems.filter((item: IMotion) => {
            let matches = true;
            Object.keys(this.filters).forEach(key => {
                if (!this.filters[key](item)) {
                    matches = false;
                }
            });
            return matches;
        });

        this.sortedFilteredItems.sort(IMotion.compareTitlePrefix);
    }

    public trackElement(index: number, element: CollectionItem) {
        return element ? element.getTrackId() : null;
    }

    private callBackend(data: URLSearchParams) {
        data.set(this.csrfParam, this.csrfToken);
        return this._http
            .post(this.ajaxBackendUrl, data.toString(), {
                headers: new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
            });
    }

    public motionScreen(item: Motion, $event) {
        $event.preventDefault();

        let params = new URLSearchParams();
        params.set('operation', 'motionScreen');
        params.set('motionId[]', item.id);
        this.callBackend(params).subscribe((returnValue) => {
            console.log(returnValue);
        });
    }

    public motionUnscreen(item: Motion, $event) {
        $event.preventDefault();

        let params = new URLSearchParams();
        params.set('operation', 'motionUnscreen');
        params.set('motionId[]', item.id);
        this.callBackend(params).subscribe((returnValue) => {
            console.log(returnValue);
        });
    }

    public motionCreateTpl(item: Motion, $event) {
        $event.preventDefault();
    }

    public motionDelete(item: Motion, $event) {
        $event.preventDefault();
    }

    public amendmentScreen(item: Amendment, $event) {
        $event.preventDefault();
    }

    public amendmentUnscreen(item: Amendment, $event) {
        $event.preventDefault();
    }

    public amendmentCreateTpl(item: Amendment, $event) {
        $event.preventDefault();
    }

    public amendmentDelete(item: Amendment, $event) {
        $event.preventDefault();
    }

    private regexescape(str: string): RegExp {
        return new RegExp(str.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), "i");
    }

    public getHighlightedTitle(item: IMotion): string {
        let html = item.getTitle().replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
        if (this.searchTitle !== '') {
            let search = this.searchTitle.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
            html = html.replace(this.regexescape(search), (match) => {
                return '<mark>' + match + '</mark>';
            });
        }
        return html;
    }

    public getHighlightedInitiator(item: IMotion): string {
        let html = item.getInitiatorName().replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
        if (this.searchInitiator !== '') {
            let search = this.searchInitiator.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
            html = html.replace(this.regexescape(search), (match) => {
                return '<mark>' + match + '</mark>';
            });
        }
        return html;
    }

    public getHighlightedPrefix(item: IMotion): string {
        let html = item.titlePrefix.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
        if (this.searchPrefix !== '') {
            let search = this.searchPrefix.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
            html = html.replace(this.regexescape(search), (match) => {
                return '<mark>' + match + '</mark>';
            });
        }
        if (html === '') {
            html = '-';
        }
        return html;
    }

    public getStatusString(item: IMotion): string {
        let status = Translations.getStatusName(item.status);
        if (item.status === STATUS.COLLECTING_SUPPORTERS) {
            status += ' (' + item.supporters.length.toString(10) + ')';
        }
        return status;
    }

    private sortAndAddZeroItems(items: SelectlistItem[]): SelectlistItem[] {
        items.sort((obj1, obj2) => {
            if (obj2.num > obj1.num) {
                return 1;
            } else if (obj2.num < obj1.num) {
                return -1;
            } else {
                return 0;
            }
        });
        items.unshift({
            id: "0",
            title: Translations.get('admin', 'filter_na'),
            num: null,
        });
        return items;
    }

    public getAvailableStatusItems(): SelectlistItem[] {
        if (!this.allItems) {
            return [];
        }
        let statuses = {};
        this.allItems.forEach((item) => {
            const status = item.status.toString();
            if (statuses[status] === undefined) {
                statuses[status] = 0;
            }
            statuses[status]++;
        });

        return this.sortAndAddZeroItems(Object.keys(statuses).map((status: string) => {
            return {
                id: status,
                title: Translations.getStatusName(parseInt(status)),
                num: statuses[status],
            }
        }));
    }

    public getAvailableTagsItems(): SelectlistItem[] {
        if (!this.allItems) {
            return [];
        }
        let tags = {};
        this.allItems.forEach((item) => {
            item.tags.forEach((tag) => {
                const tagId = tag.id.toString();
                if (tags[tagId] === undefined) {
                    tags[tagId] = 0;
                }
                tags[tagId]++;
            });
        });

        return this.sortAndAddZeroItems(Object.keys(tags).map((tag: string) => {
            return {
                id: tag,
                title: Translations.getTagName(tag),
                num: tags[tag],
            }
        }));
    }

    public searchPrefixChange($ev) {
        if ($ev.currentTarget.value === this.searchPrefix) {
            return;
        }
        this.searchPrefix = $ev.currentTarget.value;
        if (this.searchPrefix === '') {
            delete this.filters['prefix'];
        } else {
            this.filters['prefix'] = (motion: IMotion) => {
                return motion.titlePrefix.toLowerCase().indexOf(this.searchPrefix.toLowerCase()) !== -1;
            };
        }
        this.recalcMotionList();
    }

    public searchTitleChange($ev) {
        if ($ev.currentTarget.value === this.searchTitle) {
            return;
        }
        this.searchTitle = $ev.currentTarget.value;
        if (this.searchTitle === '') {
            delete this.filters['title'];
        } else {
            this.filters['title'] = (motion: IMotion) => {
                return motion.getTitle().toLowerCase().indexOf(this.searchTitle.toLowerCase()) !== -1;
            };
        }
        this.recalcMotionList();
    }

    public searchInitiatorChange($ev) {
        if ($ev.currentTarget.value === this.searchInitiator) {
            return;
        }
        this.searchInitiator = $ev.currentTarget.value;
        if (this.searchInitiator === '') {
            delete this.filters['initiator'];
        } else {
            this.filters['initiator'] = (motion: IMotion) => {
                return motion.getInitiatorName().toLowerCase().indexOf(this.searchInitiator.toLowerCase()) !== -1;
            };
        }
        this.recalcMotionList();
    }

    public setStatusItem(selected) {
        if (parseInt(selected.id) === 0) {
            delete this.filters['status'];
        } else {
            this.filters['status'] = (motion: IMotion) => {
                return motion.status == selected.id;
            };
        }
        this.recalcMotionList();
    }

    public setTagItem(selected) {
        if (parseInt(selected.id) === 0) {
            delete this.filters['tag'];
        } else {
            this.filters['tag'] = (motion: IMotion) => {
                return motion.tags.filter(tag => tag.id === parseInt(selected.id)).length > 0;
            };
        }
        this.recalcMotionList();
    }
}
